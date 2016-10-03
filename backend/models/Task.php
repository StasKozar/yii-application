<?php

namespace backend\models;

use Yii;
use backend\models\Helper;
use backend\models\WorkSchedule;
use \yii\db\ActiveRecord;
use tuyakhov\jsonapi\ResourceTrait;


/**
 * This is the model class for table "time_task".
 *
 * @property integer $id
 * @property string $begin
 * @property string $end
 */
class Task extends ActiveRecord
{
    use ResourceTrait;

    public function getWorkScheduleDays()
    {
        return $workDays = WorkSchedule::find()->select('day')->column();
    }

    public function getWorkScheduleWorkTime()
    {
        $begin = WorkSchedule::find()->select('begin')->column();
        $end = WorkSchedule::find()->select('end')->column();

        return $workTime = [
            'begin' => $begin[0],
            'end' => $end[0]
        ];
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
            [['begin', 'end'], 'required'],
            [['begin', 'end'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'begin' => 'Begin of task',
            'end' => 'End of task',
        ];
    }

    public function getTime()
    {
        $B = 2;
        $U = 0;
        $F = 1;
        $model = $this;
        $tasks = $this::find()->all();
        $workDays = $this::getWorkScheduleDays();
        $workTime = $this::getWorkScheduleWorkTime();
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
                $periodType = $U;
                $searchPeriod[] = new Helper($start, $end, $periodType);
                $start = $date->format('Y-m-d').' '.$from;
                $end = $date->format('Y-m-d').' '.$to;
                $periodType = $F;
                $searchPeriod[] = new Helper($start, $end, $periodType);
                $start = $date->format('Y-m-d').' '.$to;
                $end = $date->format('Y-m-d 23:59');
                $periodType = $U;
                $searchPeriod[] = new Helper($start, $end, $periodType);
            } else {
                $start = $date->format('Y-m-d H:i');
                $end = $date->format('Y-m-d 23:59');
                $periodType = $U;
                $searchPeriod[] = new Helper($start, $end, $periodType);
            }
        }

        $workPeriod = [];
        foreach ($tasks as $task){
            $start = substr($task['begin'], 0, -3);
            $end = substr($task['end'], 0, -3);
            $periodType = $B;
            $workPeriod[] = new Helper($start, $end, $periodType);
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
                    $periodType = $work->periodType;
                    $searchPeriod[] = new Helper($start, $end, $periodType);
                    $searchPeriod[$i]->end = $work->begin;
                    break;
                }elseif (substr($work->begin, 0, -6) < $beginPeriod->format('Y-m-d')
                    && substr($work->end, 0, -6) == substr($searchPeriod[$i]->end, 0, -6)){
                    if($searchPeriod[$i]->end > $work->end)
                    {
                        $start = $searchPeriod[$i]->begin;
                        $end = $work->end;
                        $periodType = $work->periodType;
                        $searchPeriod[] = new Helper($start, $end, $periodType);
                        $searchPeriod[$i]->begin = $work->end;
                        break;
                    }elseif($searchPeriod[$i]->end < $work->end){
                        $searchPeriod[$i]->end = $work->end;
                        $searchPeriod[$i]->type = $work->periodType;
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
                            $periodType = $searchPeriod[$i]->periodType;
                            $searchPeriod[] = new Helper($start, $end, $periodType);
                            $searchPeriod[$i]->end = $work->begin;
                            sort($searchPeriod);
                        }
                    }
                }
            }
        }

        return $this->getApiTime($searchPeriod, $period);

    }

    public function getApiTime($searchPeriod, $period=null)
    {
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

        return [
            'begin' => $begin,
            'end' => $end,
        ];

    }
}
