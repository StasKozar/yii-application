<?php

namespace backend\models;

use backend\models\Helper;

class GeneratePeriod
{
    const PERIOD_TYPE_BUSY = 2;
    const PERIOD_TYPE_FREE = 1;
    const PERIOD_TYPE_UNAVAILABLE = 0;

    public static function createPeriod($period, $workDays, $workTimeBegin, $workTimeEnd)
    {
        $result = [];

        foreach ($period as $date) {
            if (in_array($date->format('w'), $workDays)) {
                $start = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d H:i'));
                $end = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $workTimeBegin);
                $periodType = self::PERIOD_TYPE_UNAVAILABLE;
                $result[] = new Helper($start, $end, $periodType);
                $start = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $workTimeBegin);
                $end = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $workTimeEnd);
                $periodType = self::PERIOD_TYPE_FREE;
                $result[] = new Helper($start, $end, $periodType);
                $start = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $workTimeEnd);
                $end = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d 23:59'));
                $periodType = self::PERIOD_TYPE_UNAVAILABLE;
                $result[] = new Helper($start, $end, $periodType);
            } else {
                $start = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d H:i'));
                $end = date_create_from_format('Y-m-d H:i', $date->format('Y-m-d 23:59'));
                $periodType = self::PERIOD_TYPE_UNAVAILABLE;
                $result[] = new Helper($start, $end, $periodType);
            }
        }
        return $result;
    }

    public static function createWorkPeriod($tasks)
    {
        $result = [];
        foreach ($tasks as $task) {
            $start = date_create_from_format('Y-m-d H:i', substr($task['begin'], 0, -3));
            $end = date_create_from_format('Y-m-d H:i', substr($task['end'], 0, -3));
            $periodType = self::PERIOD_TYPE_BUSY;
            $result[] = new Helper($start, $end, $periodType);
        }
        return $result;
    }

    public static function merge($period)
    {
        for ($i = 0, $j = 1; $i < count($period); $i++, $j++) {
            if (isset($period[$j])) {
                if ($period[$i]->begin <= $period[$j]->begin && $period[$j]->begin <= $period[$i]->end) {
                    $period[$i]->end = $period[$j]->end;
                    array_splice($period, $j, 1);
                } elseif ($period[$j]->begin >= $period[$i]->begin && $period[$j]->end <= $period[$i]->end) {
                    $period[$i]->begin = $period[$j]->begin;
                    $period[$i]->end = $period[$j]->end;
                    array_splice($period, $j, 1);
                }
            }
        }
        return $period;
    }

    public static function generateSearchPeriod($beginPeriod, $endPeriod, $workPeriod, $searchPeriod)
    {
        foreach ($workPeriod as $work) {
            for ($i = 0, $j = 1; $i < count($searchPeriod); $i++, $j++) {
                if ($work->begin->format('Y-m-d') == $searchPeriod[$i]->begin->format('Y-m-d')
                    && $work->end->format('Y-m-d') > $endPeriod->format('Y-m-d')
                ) {
                    $start = $work->begin;
                    $end = $searchPeriod[$i]->end;
                    $periodType = $work->periodType;
                    $searchPeriod[] = new Helper($start, $end, $periodType);
                    $searchPeriod[$i]->end = $work->begin;
                    break;
                } elseif ($work->begin->format('Y-m-d') < $beginPeriod->format('Y-m-d')
                    && $work->end->format('Y-m-d') == $searchPeriod[$i]->end->format('Y-m-d')
                ) {
                    if ($searchPeriod[$i]->end > $work->end) {
                        $start = $searchPeriod[$i]->begin;
                        $end = $work->end;
                        $periodType = $work->periodType;
                        $searchPeriod[] = new Helper($start, $end, $periodType);
                        $searchPeriod[$i]->begin = $work->end;
                        break;
                    } elseif ($searchPeriod[$i]->end < $work->end) {
                        $searchPeriod[$i]->end = $work->end;
                        $searchPeriod[$i]->type = $work->periodType;
                        $searchPeriod[$j]->begin = $work->end;
                    }
                } elseif ($work->begin->format('Y-m-d') == $searchPeriod[$i]->begin->format('Y-m-d')) {
                    if (isset($searchPeriod[$j])) {
                        if ($work->begin > $searchPeriod[$i]->begin
                            && $searchPeriod[$i]->end < $work->end
                            && $work->begin < $searchPeriod[$i]->end
                        ) {
                            $searchPeriod[$i]->end = $work->begin;
                            $searchPeriod[] = $work;
                            $searchPeriod[$j]->begin = $work->end;
                        } elseif ($work->begin > $searchPeriod[$i]->begin
                            && $searchPeriod[$i]->end > $work->end) {
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
        sort($searchPeriod);
        return $searchPeriod;
    }
}