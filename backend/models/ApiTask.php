<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 30/09/2016
 * Time: 17:11
 */

namespace backend\models;


class ApiTask extends Task
{
    public function getApiTime($searchPeriod, $period=null)
    {
        return [
            $searchPeriod,
        ];
    }
}