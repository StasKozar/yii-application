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
        $U = 'U';
        $F = 'F';
        $B = 'B';
        $model = $this;
        $tasks = $this::find()->all();
        $workPeriod = [];
        $searchPeriod = [];
        $result = [];
        $period = [];
        $notAvailablePeriod = [];
        $taskPeriod = [];
        $workTime = $this::getWorkTime();
        $from = date('H:i', $workTime['begin']);
        $to = date('H:i', $workTime['end']);
        $workDays = $this::getWorkDays();

        foreach ($tasks as $key => $value) {
            $value_begin = new \DateTime($value->attributes['begin']);
            $value_end = new \DateTime($value->attributes['end']);
            $taskPeriod[] = $value_begin->format('Y-m-d');

            $workPeriod[$key]['begin'] = new \DateTime($value_begin->format('Y-m-d H:i'));
            $workPeriod[$key]['end'] = new \DateTime($value_end->format('Y-m-d H:i'));
        }

        sort($workPeriod);
        $begin = new \DateTime($model->begin);
        $date_diff = $begin->diff(new \DateTime($model->end));

        for ($i = 0; $i <= $date_diff->days; $i++) {
            if (in_array($begin->format('w'), $workDays)) {
                $period[$i][] = new \DateTime($begin->format('Y-m-d') . ' ' . $from);
                $period[$i][] = new \DateTime($begin->format('Y-m-d') . ' ' . $to);
            }
            $notAvailablePeriod[$i][] = new \DateTime($begin->format('Y-m-d H:i'));
            $notAvailablePeriod[$i][] = new \DateTime($begin->format('Y-m-d 23:59'));
            $begin->add(new \DateInterval('P1D'));
        }

        foreach ($notAvailablePeriod as $key => $value) {
            if ($value[0]->format('Y-m-d') !== $period[$key][0]->format('Y-m-d')) {
                $result[] = $value[0]->format('Y-m-d H:i').$U;
                $result[] = $value[1]->format('Y-m-d H:i').$U;
            }
            for ($i = 0; $i < count($period); $i++) {
                if ($value[0]->format('Y-m-d') == $period[$i][0]->format('Y-m-d')) {
                    if ($value[0]->format('Y-m-d H:i') == $period[$i][0]->format('Y-m-d H:i')
                        && $value[1]->format('Y-m-d H:i') == $period[$i][1]->format('Y-m-d H:i')
                    ) {
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$F;
                    } elseif ($value[0]->format('Y-m-d H:i') == $period[$i][0]->format('Y-m-d H:i')) {
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$U;
                        $result[] = $value[1]->format('Y-m-d H:i').$U;
                    } elseif ($value[1]->format('Y-m-d H:i') == $period[$i][1]->format('Y-m-d H:i')) {
                        $result[] = $value[0]->format('Y-m-d H:i').$U;
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$U;
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$F;
                    } else {
                        $result[] = $value[0]->format('Y-m-d H:i').$U;
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$U;
                        $result[] = $period[$i][0]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$F;
                        $result[] = $period[$i][1]->format('Y-m-d H:i').$U;
                        $result[] = $value[1]->format('Y-m-d H:i').$U;
                    }
                }
            }
        }

        while (list($key, $value) = each($result)) {
            if (strpos($value, $F) > 0) {
                if (!in_array(substr($value, 0, -7), $taskPeriod))
                {
                    $searchPeriod[] = $value;
                }
                for ($i = 0; $i < count($workPeriod); $i++) {
                    if (substr($value, 0, -7) == $workPeriod[$i]['begin']->format('Y-m-d')) {
                        if (substr($value, 0, -1) == $workPeriod[$i]['begin']->format('Y-m-d H:i')
                            && substr($result[$key + 1], 0, -1) == $workPeriod[$i]['end']->format('Y-m-d H:i')
                        ) {
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $B;
                        } elseif (substr($value, 0, -1) == $workPeriod[$i]['end']->format('Y-m-d H:i')
                            && substr($result[$key - 1], 0, -1) == $workPeriod[$i]['begin']->format('Y-m-d H:i')
                        ) {
                            continue;
                        } elseif (substr($value, 0, -1) == $workPeriod[$i]['begin']->format('Y-m-d H:i')) {
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $F;
                        } elseif (substr($value, 0, -1) == $workPeriod[$i]['end']->format('Y-m-d H:i')
                            && substr($result[$key - 1], 0, -1) != $workPeriod[$i]['begin']->format('Y-m-d H:i')
                        ) {
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $F;
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $B;
                        } elseif (substr($value, 0, -1) < $workPeriod[$i]['begin']->format('Y-m-d H:i')
                            && substr($result[$key + 1], 0, -1) > $workPeriod[$i]['end']->format('Y-m-d H:i')
                        ) {
                            $searchPeriod[] = $value;
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $F;
                            $searchPeriod[] = $workPeriod[$i]['begin']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $B;
                            $searchPeriod[] = $workPeriod[$i]['end']->format('Y-m-d H:i') . $F;
                        } else {
                            $searchPeriod[] = $value;
                        }
                    }
                }
            } else {
                $searchPeriod[] = $value;
            }
        }


        $searchPeriod = array_unique($searchPeriod);
        return [
            'time' => $searchPeriod,
            'period' => $notAvailablePeriod,
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
