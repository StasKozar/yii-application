<?php

namespace backend\models;

use Faker\Provider\cs_CZ\DateTime;
use Yii;

/**
 * This is the model class for table "time_task".
 *
 * @property integer $id
 * @property string $name
 * @property string $begin
 * @property string $end
 * @property integer $active
 */
class Task extends \yii\db\ActiveRecord
{
    public $workTime;
    public $workDays;


    public static function getWorkTime()
    {
        return [
            'begin' => 8 * 60 * 60,
            'end' => 20 * 60 * 60
        ];
    }

    public static function getWorkDays()
    {
        return [0, 1, 2, 3, 4, 5, 6];
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
        $model = $this;
        $result = null;
        $tasks = $this::find()->all();
        $workPeriod = [];
        $testPeriod = [];
        $workTime = $this::getWorkTime();
        $from = date('H:i', $workTime['begin']);
        $to = date('H:i', $workTime['end']);
        $workDays = $this::getWorkDays();

        foreach ($tasks as $key => $value) {
            $value_begin = new \DateTime($value->attributes['begin']);
            $value_end = new \DateTime($value->attributes['end']);
            $testPeriod[] = ($value_begin->format('Y-m-d'));
            $testPeriod[] = ($value_end->format('Y-m-d'));

            $workPeriod[$key]['begin'] = new \DateTime($value_begin->format('Y-m-d H:i'));
            $workPeriod[$key]['end'] = new \DateTime($value_end->format('Y-m-d H:i'));
        }

        $begin = new \DateTime($model->begin);
        $date_diff = $begin->diff(new \DateTime($model->end));
        $period = [];
        $notAvailablePeriod = [];
        for ($i = 0; $i <= $date_diff->days; $i++) {
            if (in_array($begin->format('w'), $workDays)) {
                $period[$i][] = new \DateTime($begin->format('Y-m-d') . ' ' . $from);
                $period[$i][] = new \DateTime($begin->format('Y-m-d') . ' ' . $to);
                $notAvailablePeriod[] = ($begin->format('Y-m-d H:i'));
                $notAvailablePeriod[] = ($begin->format('Y-m-d 23:59'));
            }
            $begin->add(new \DateInterval('P1D'));
        }

        $freePeriod = [];
        foreach ($period as $key => $value) {
            if (!in_array($value[0]->format('Y-m-d'), $testPeriod)) {
                $freePeriod[] = $value[0]->format('Y-m-d H:i');
                $freePeriod[] = $value[1]->format('Y-m-d H:i');
            }
            for ($i = 0; $i < count($workPeriod); $i++) {
                if ($value[0]->format('Y-m-d') == $workPeriod[$i]['begin']->format('Y-m-d')) {
                    if ($value[0]->format('Y-m-d H:i') == $workPeriod[$i]['begin']->format('Y-m-d H:i')
                        && $value[1]->format('Y-m-d H:i') == $workPeriod[$i]['end']->format('Y-m-d H:i')
                    ) {
                        continue;
                    } elseif ($value[0]->format('Y-m-d H:i') == $workPeriod[$i]['begin']->format('Y-m-d H:i')) {
                        $freePeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i');
                        $freePeriod[] = $value[1]->format('Y-m-d H:i');

                    } elseif ($value[1]->format('Y-m-d H:i') == $workPeriod[$i]['end']->format('Y-m-d H:i')) {
                        $freePeriod[] = $value[0]->format('Y-m-d H:i');
                        $freePeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i');

                    } else {
                        $freePeriod[] = $value[0]->format('Y-m-d H:i');
                        $freePeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i');
                        $freePeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i');
                        $freePeriod[] = $value[1]->format('Y-m-d H:i');
                    }
                }
            }
            $period[$key][0] = $value[0]->format('Y-m-d H:i');
            $period[$key][1] = $value[1]->format('Y-m-d H:i');
        }
        /*for($i = 0; $i < count($workPeriod); $i++){
            for($j = 0; $j < count($period); $j++){
                if($workPeriod[$i]['begin']->format('Y-m-d') == $period[$j]['begin']->format('Y-m-d')){
                    $period[$j][] = $workPeriod[$i]['begin'];
                    $period[$j][] = $workPeriod[$i]['end'];
                }
            }
        }*/

        $busyPeriod = [];
        for ($i = 0; $i<count($workPeriod); $i++)
        {
            $busyPeriod[$i]['begin'] = $workPeriod[$i]['begin']->format('Y-m-d H:i');
            $busyPeriod[$i]['end'] = $workPeriod[$i]['end']->format('Y-m-d H:i');
        }
        sort($busyPeriod);

        $busyTime = $busyPeriod;

        asort($freePeriod);
        $freeTime = array_unique($freePeriod);
        return [
            'freeTime' => $period,
            'busyTime' => $busyPeriod,
            'notAvailableTime' => $notAvailablePeriod
        ];
    }

    public function validateDate()
    {
        $tasks = $this::find()->all();
        $model = $this;

        $task_begin = new \DateTime($model->begin);
        $task_end = new \DateTime($model->end);
        $workTime = $this::getWorkTime();
        $workDays = $this::getWorkDays();

        if($task_begin >= $task_end){
            return [
                'message' => 'Date of begin can\'t be bigger or equal then date of end ',
            ];
        }

        if (in_array(date_format($task_begin, 'w'), $workDays)
            && in_array(date_format($task_end, 'w'), $workDays))
        {
            if (date('H:i', $workTime['begin']) <= date_format($task_begin, 'H:i')
                && date('H:i', $workTime['end']) >= date_format($task_end, 'H:i'))
            {
                if ($tasks === null)
                {
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
            return ['message' => 'Please choose another time!',];
        }else{
            return ['message' => 'Please choose another date!',];
        }
    }
}
