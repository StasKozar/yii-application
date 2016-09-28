<form action="timetask.php" method="post">
    Date of begin<input type="datetime-local" name="task_begin" title="begin">
    <br>
    Date of end<input type="datetime-local" name="task_end" title="end">
    <br>
    <input type="submit" value="Add">
</form>
<?php
$tasks = [
    'task1' => [
        'begin' => new DateTime('2016-09-20 15:12:00'),
        'end' => new DateTime('2016-09-20 16:12:00'),
    ],
    'task2' => [
        'begin' => new DateTime('2016-09-20 15:05:00'),
        'end' => new DateTime('2016-09-20 15:10:00'),
    ],
    'task3' => [
        'begin' => new DateTime('2016-09-20 08:15:00'),
        'end' => new DateTime('2016-09-20 08:25:00'),
    ]
];

$worktime = [
    'begin' => 07*60*60,
    'end' => 19*60*60,
    'workdays' => [1, 2, 3, 4, 5],
    'holidays' => [0, 6],
];

if(($_POST['task_begin'] >= $_POST['task_end'])){
    echo 'false';
    return false;
}
else
{
    $task_begin = new DateTime($_POST['task_begin']);
    $task_end = new DateTime($_POST['task_end']);


    if (in_array(date_format($task_begin, 'w'), $worktime['workdays'])
        && in_array(date_format($task_end, 'w'), $worktime['workdays'])
    ) {
        if (date('H:i:s', $worktime['begin']) <= date_format($task_begin, 'H:i:s')
            && date('H:i:s', $worktime['end']) >= date_format($task_end, 'H:i:s')
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
                echo $task . '<br>';
                if (!array_key_exists($task, $tasks)/*$key !== $task*/) {
                    $tasks[$task]['begin'] = $temp_begin;
                    $tasks[$task]['end'] = $temp_end;
                    var_dump($tasks);
                    die();
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

?>