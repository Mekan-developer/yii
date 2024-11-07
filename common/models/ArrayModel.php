<?php

namespace common\models;

abstract class ArrayModel extends \yii\base\Model
{
    static protected $_list = [];

    public static function listAll()
    {
        return static::$_list;
    }

    public static function findOne($id)
    {
        return static::$_list[$id] ?? '';
    }

    public static function getIdByName($value)
    {
        $value = trim($value);
        if (!$value) return 0;
        $value = mb_strtolower($value);
        $found = 0;
        foreach (static::$_list as $id => $val) {
            if (mb_strtolower($val) == $value) {
                $found = $id;
            }
        }
        return $found;
    }
}
