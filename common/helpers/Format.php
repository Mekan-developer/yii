<?php

namespace common\helpers;

class Format
{
    static public function numberClean($val, $decimals = 0)
    {
        if ($decimals == 0) return (int)$val;
        $val = number_format($val, $decimals, '.', '');
        $val = rtrim($val, '0');
        $val = rtrim($val, '.');
        return $val;
    }
}
