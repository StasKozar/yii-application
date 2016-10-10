<?php

namespace api\modules\v1\models;

use components\jsonapi\ResourceTrait;


class Task extends \backend\models\Task
{
    use ResourceTrait;

    public function fields()
    {
        return [
            'begin',
            'end',
        ];
    }
    public function extraFields()
    {
        return ['id'];
    }

    public $message = false;

    public function getApiTime($searchPeriod, $period=null)
    {
        foreach ($searchPeriod as $key => $value){
            $searchPeriod[$key] = [
                'type' => $this->getType(),
                'attributes' => [
                    'begin' => $value->begin->format('Y-m-d H:i'),
                    'end' => $value->end->format('Y-m-d H:i'),
                    'type' => $value->periodType,
                    ]
            ];
        }
        return [
            $searchPeriod,
        ];
    }

    public function validateDate()
    {
        $tasks = parent::find()->all();
        $model = $this;

        if(\DateTime::createFromFormat('Y-m-d H:i' ,$model['begin']) === false
            || \DateTime::createFromFormat('Y-m-d H:i', $model['end']) === false)
        {
            return $model->message = 'Date do not must be a string and format to Y-m-d H:i';
        }

        $task_begin = new \DateTime($model['begin']);
        $task_end = new \DateTime($model['end']);

        if($task_begin >= $task_end){
            return $model->message = 'Date of begin can\'t be bigger then date of end';
        }
        $temp_begin = '';
        $temp_end = '';
        foreach ($tasks as $key => $value) {
            $value_begin = new \DateTime($value->attributes['begin']);
            $value_end = new \DateTime($value->attributes['end']);

            if (($value_begin > $task_begin || $value_end < $task_begin)
                && ($value_begin > $task_end || $value_end < $task_end)
                && !($task_begin < $value_begin && $value_end < $task_end)
                && !($task_begin > $value_begin && $value_end > $task_end))
            {
                $temp_begin = (array)$task_begin;
                $temp_end = (array)$task_end;

            } else {
                return $model->message = 'Please choose another date';
            }
        }
        $begin = $temp_begin['date'];
        $end = $temp_end['date'];

        return [
            $begin,
            $end,
        ];

    }
}