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
    const PERIOD_TYPE_BUSY = 2;
    const PERIOD_TYPE_FREE = 1;
    const PERIOD_TYPE_UNAVAILABLE = 0;
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
        $model = $this;
        $tasks = $this::find()->all();
        $workDays = $this::getWorkScheduleDays();
        $workTime = $this::getWorkScheduleWorkTime();
        $workTimeBegin = date('H:i', $workTime['begin']);
        $workTimeEnd = date('H:i', $workTime['end']);
        $beginPeriod = new \DateTime($model->begin);
        $endPeriod = new \DateTime($model->end);
        $recurrences = $beginPeriod->diff($endPeriod);
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($beginPeriod, $interval, $recurrences->days);

        $searchPeriod = GeneratePeriod::createPeriod($period, $workDays, $workTimeBegin, $workTimeEnd);
        $workPeriod = GeneratePeriod::createWorkPeriod($tasks);
        $workPeriod = GeneratePeriod::merge($workPeriod);
        $searchPeriod = GeneratePeriod::generateSearchPeriod($beginPeriod, $endPeriod, $workPeriod, $searchPeriod);

        return $this->getApiTime($searchPeriod, $period);

    }

    public function getApiTime($searchPeriod, $period = null)
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

        if ($task_begin >= $task_end) {
            return ['message' => 'Please choose another date'];
        }
        $temp_begin = '';
        $temp_end = '';
        foreach ($tasks as $key => $value) {
            $value_begin = new \DateTime($value->attributes['begin']);
            $value_end = new \DateTime($value->attributes['end']);

            if (($value_begin > $task_begin || $value_end < $task_begin)
                && ($value_begin > $task_end || $value_end < $task_end)
                && !($task_begin < $value_begin && $value_end < $task_end)
            ) {
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
