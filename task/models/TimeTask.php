<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 21/09/2016
 * Time: 10:15
 */

namespace task\models;

use yii\base\Model;

class TimeTask extends Model
{
    public $workTime;
    public $tasks;
    public $taskEnd;
    public $taskBegin;
    public $answer;

    public function attributeLabels()
    {
        return [
            'workTime' => 'Work Time',
            'tasks' => 'Tasks',
            'taskEnd' => 'Task end',
            'taskBegin' => 'Task begin',
        ];
    }

    public function getData()
    {
        $this->workTime = [
            'begin' => 07*60*60,
            'end' => 19*60*60,
            'workdays' => [1, 2, 3, 4, 5],
            'holidays' => [0, 6],
        ];

        $this->tasks = [
            'task1' => [
                'begin' => '2016-09-20 15:12:00',
                'end' => '2016-09-20 16:12:00',
            ],
            'task2' => [
                'begin' => '2016-09-20 15:05:00',
                'end' => '2016-09-20 15:10:00',
            ],
            'task3' => [
                'begin' => '2016-09-20 08:15:00',
                'end' => '2016-09-20 08:25:00',
            ]
        ];

        $this->taskBegin = '2016-09-20 14:05:00';
        $this->taskEnd = '2016-09-20 14:10:00';

    }

    public function getAnswer()
    {
        $model = new TimeTask();
        $model->getData();
        if(($model->taskBegin >= $model->taskEnd)){
            echo 'false';
            return false;
        }
        else
        {
            $task_begin = $model->taskBegin;
            $task_end = $model->taskEnd;
            $workTime = $model->workTime;
            $tasks = $model->tasks;


            if (in_array(date_format($task_begin, 'w'), $workTime['workdays'])
                && in_array(date_format($task_end, 'w'), $workTime['workdays'])
            ) {
                if (date('H:i:s', $workTime['begin']) <= date_format($task_begin, 'H:i:s')
                    && date('H:i:s', $workTime['end']) >= date_format($task_end, 'H:i:s')
                ) {
                    $temp_begin = '';
                    $temp_end = '';
                    foreach ($tasks as $key => $value) {

                        if (($value['begin'] > $task_begin || $value['end'] < $task_begin)
                            && ($value['begin'] > $task_end || $value['end'] < $task_end)
                            && !($task_begin < $value['begin'] && $value['end'] < $task_end)
                        ) {
                            $temp_begin = $task_begin;
                            $temp_end = $task_end;

                        } else {
                            echo 'false';
                            return false;
                        }
                    }

                    $i = 1;
                    while (true) {
                        $task = 'task' . $i;

                        if (!array_key_exists($task, $tasks)) {
                            $tasks[$task]['begin'] = $temp_begin;
                            $tasks[$task]['end'] = $temp_end;
                            $model->answer = 'true';
                        }
                        $i++;
                    }
                } else {
                    echo 'false';
                }
            } else {
                echo 'false';
            }
        }
    }
}