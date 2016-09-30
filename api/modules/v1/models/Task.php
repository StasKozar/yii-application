<?php

namespace api\modules\v1\models;

use Yii;
use api\modules\v1\models\Helper;
use \yii\db\ActiveRecord;


/**
 * This is the model class for table "time_task".
 *
 * @property integer $id
 * @property string $name
 * @property string $begin
 * @property string $end
 * @property integer $active
 */
class Task extends ActiveRecord
{
    public $workTime;
    public $workDays;


    public function fields()
    {
        return [
            'begin',
            'end',
            /*'period' => function($model){
                return $model->getTime();
            },*/
        ];
    }
    public function extraFields()
    {
        return ['name', 'id', 'active'];
    }


    public static function getWorkTime()
    {
        return [
            'begin' => 8 * 60 * 60,
            'end' => 17 * 60 * 60
        ];
    }

    public static function getWorkDays()
    {
        return [1, 2, 4, 5,];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'time_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'begin', 'end'], 'required'],
            [['begin', 'end'], 'safe'],
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'begin' => 'Begin of task',
            'end' => 'End of task',
            'active' => 'Active',
        ];
    }

    public function getTime()
    {
        $B = 'Busy';
        $U = 'Unavailable';
        $F = 'Free';
        $model = $this;
        $tasks = $this::find()->all();
        $workDays = $this::getWorkDays();
        $workTime = $this::getWorkTime();
        $from = date('H:i', $workTime['begin']);
        $to = date('H:i', $workTime['end']);
        $beginPeriod = new \DateTime($model->begin);
        $endPeriod = new \DateTime($model->end);
        $recurrences = $beginPeriod->diff($endPeriod);
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($beginPeriod, $interval, $recurrences->days);

        $searchPeriod = [];
        foreach ($period as $date)
        {
            if(in_array($date->format('w'), $workDays)){
                $start = $date->format('Y-m-d H:i');
                $end = $date->format('Y-m-d').' '.$from;
                $type = $U;
                $searchPeriod[] = new Helper($start, $end, $type);
                $start = $date->format('Y-m-d').' '.$from;
                $end = $date->format('Y-m-d').' '.$to;
                $type = $F;
                $searchPeriod[] = new Helper($start, $end, $type);
                $start = $date->format('Y-m-d').' '.$to;
                $end = $date->format('Y-m-d 23:59');
                $type = $U;
                $searchPeriod[] = new Helper($start, $end, $type);
            } else {
                $start = $date->format('Y-m-d H:i');
                $end = $date->format('Y-m-d 23:59');
                $type = $U;
                $searchPeriod[] = new Helper($start, $end, $type);
            }
        }

        $workPeriod = [];
        foreach ($tasks as $task){
            $start = substr($task['begin'], 0, -3);
            $end = substr($task['end'], 0, -3);
            $type = $B;
            $workPeriod[] = new Helper($start, $end, $type);
        }

        for($i=0, $j=1; $i <count($workPeriod); $i++, $j++){
            if(isset($workPeriod[$j])){
                if($workPeriod[$i]->begin <= $workPeriod[$j]->begin && $workPeriod[$j]->begin <= $workPeriod[$i]->end)
                {
                    $workPeriod[$i]->end = $workPeriod[$j]->end;
                    array_splice($workPeriod, $j, 1);
                }elseif($workPeriod[$j]->begin >= $workPeriod[$i]->begin && $workPeriod[$j]->end <= $workPeriod[$i]->end){
                    $workPeriod[$i]->begin = $workPeriod[$j]->begin;
                    $workPeriod[$i]->end = $workPeriod[$j]->end;
                    array_splice($workPeriod, $j, 1);
                }
            }
        }

        foreach ($workPeriod as $work) {
            for($i=0, $j=1; $i < count($searchPeriod); $i++, $j++)
            {
                if (substr($work->begin, 0, -6) == substr($searchPeriod[$i]->begin, 0, -6)
                    && substr($work->end, 0, -6) > $endPeriod->format('Y-m-d')){
                    $start = $work->begin;
                    $end = $searchPeriod[$i]->end;
                    $type = $work->type;
                    $searchPeriod[] = new Helper($start, $end, $type);
                    $searchPeriod[$i]->end = $work->begin;
                    break;
                }elseif (substr($work->begin, 0, -6) < $beginPeriod->format('Y-m-d')
                    && substr($work->end, 0, -6) == substr($searchPeriod[$i]->end, 0, -6)){
                    if($searchPeriod[$i]->end > $work->end)
                    {
                        $start = $searchPeriod[$i]->begin;
                        $end = $work->end;
                        $type = $work->type;
                        $searchPeriod[] = new Helper($start, $end, $type);
                        $searchPeriod[$i]->begin = $work->end;
                        break;
                    }elseif($searchPeriod[$i]->end < $work->end){
                        $searchPeriod[$i]->end = $work->end;
                        $searchPeriod[$i]->type = $work->type;
                        $searchPeriod[$j]->begin = $work->end;
                    }
                }elseif(substr($work->begin, 0, -6) == substr($searchPeriod[$i]->begin, 0, -6))
                {
                    if (isset($searchPeriod[$j])) {
                        if ($work->begin > $searchPeriod[$i]->begin && $searchPeriod[$i]->end < $work->end
                            && $work->begin < $searchPeriod[$i]->end)
                        {
                            $searchPeriod[$i]->end = $work->begin;
                            $searchPeriod[] = $work;
                            $searchPeriod[$j]->begin = $work->end;
                        } elseif ($work->begin > $searchPeriod[$i]->begin && $searchPeriod[$i]->end > $work->end) {
                            $searchPeriod[] = $work;
                            $start = $work->end;
                            $end = $searchPeriod[$i]->end;
                            $type = $searchPeriod[$i]->type;
                            $searchPeriod[] = new Helper($start, $end, $type);
                            $searchPeriod[$i]->end = $work->begin;
                            sort($searchPeriod);
                        }
                    }
                }
            }
        }

        return [
            'time' => $searchPeriod,
            'period' => $period,
        ];

    }

    public function validateDate()
    {
        $tasks = $this::find()->all();
        $model = $this;

        $task_begin = new \DateTime($model->begin);
        $task_end = new \DateTime($model->end);

        if($task_begin >= $task_end){
            return ['message' => 'Please choose another date'];
        }
        if (empty($tasks)) {
            $name = 'task1';

            return ['name' => $name];
        }
        $temp_begin = '';
        $temp_end = '';
        foreach ($tasks as $key => $value) {
            $value_begin = new \DateTime($value->attributes['begin']);
            $value_end = new \DateTime($value->attributes['end']);

            if (($value_begin > $task_begin || $value_end < $task_begin)
                && ($value_begin > $task_end || $value_end < $task_end)
                && !($task_begin < $value_begin && $value_end < $task_end))
            {
                $temp_begin = (array)$task_begin;
                $temp_end = (array)$task_end;

            } else {
                return [
                    'message' => 'Please choose another time!',
                ];
            }
        }
        $begin = $temp_begin['date'];
        $end = $temp_end['date'];
        $name = 'task1';

        return [
            'begin' => $begin,
            'end' => $end,
            'name' => $name
        ];

    }
}
