<?php

namespace backend\models;

class Helper
{
    public $begin;
    public $end;
    public $periodType;

    public function __construct($begin, $end, $periodType)
    {
        $this->begin = $begin;
        $this->end = $end;
        $this->periodType = $periodType;
    }
}
