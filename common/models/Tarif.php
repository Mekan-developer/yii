<?php

namespace common\models;

class Tarif extends ArrayModel
{
    const TARIF_BP = 1;
    const TARIF_SP = 2;
    const TARIF_FLOATING = 3;

    static protected $_list = [
        1 => 'БЕЗ ПЕРЕРАСЧЕТА',
        2 => 'С ПЕРЕРАСЧЕТОМ 50%',
//        3 => 'ПЛАВАЮЩИЙ ГРАФИК',zz
    ];
}
