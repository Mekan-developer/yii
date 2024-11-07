<?php

namespace common\validators;

class DbDateValidator extends \yii\validators\DateValidator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->timeZone = 'Europe/Moscow';
        $this->timestampAttributeTimeZone = 'Europe/Moscow';
//        $this->timestampAttributeTimeZone = 'UTC';
        if (empty($this->format)) $this->format = 'php:d.m.Y';
        if (empty($this->timestampAttributeFormat)) $this->timestampAttributeFormat = 'php:Y-m-d';
        if (empty($this->timestampAttribute)) $this->timestampAttribute = $this->attributes[0] ?? '';
    }
}
