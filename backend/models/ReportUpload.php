<?php

namespace backend\models;

use yii\base\Model;

class ReportUpload extends Model
{
    public $zip = '';

    public function rules()
    {
        return [
            [['zip'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip', 'maxFiles' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'zip' => 'Zip файл',
        ];
    }
}