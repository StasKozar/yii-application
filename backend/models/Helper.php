<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 27/09/2016
 * Time: 8:47
 */

namespace backend\models;

const B = 'Busy';
const F = 'Free';
const U = 'Unavailable';

class Helper
{
    public $begin;
    public $end;
    public $type;

    public function __construct($begin, $end, $type)
    {
        $this->begin = $begin;
        $this->end = $end;
        $this->type = $type;

    }

}
