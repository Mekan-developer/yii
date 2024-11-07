<?php

namespace common\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class JsonBehavior
 */
class JsonBehavior extends Behavior
{
    /**
     * Stores a list of fields, affected by the behavior.
     * @var array
     */
    public $fields = [];

    /**
     * Restore values after save
     * @var boolean
     */
    public $restoreAfterSave = false;

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT          => 'initJson',
            ActiveRecord::EVENT_AFTER_REFRESH => 'fromJson',
            ActiveRecord::EVENT_AFTER_FIND    => 'fromJson',
            ActiveRecord::EVENT_BEFORE_INSERT => 'toJson',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'toJson',
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
        ];
    }

    /**
     * Invokes init of parent class and assigns proper values to internal _fields variable
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Restores values after data saving
     * @param $event
     */
    public function afterSave($event)
    {
        if (!$this->restoreAfterSave) return;
        $this->fromJson($event);
    }

    /**
     * Converts parameter into JSON string
     * @param $event
     */
    public function toJson($event)
    {
        /**
         * @var $model \yii\db\ActiveRecord
         */
        $model = $this->owner;
        
        foreach ($this->fields as $attributeName => $isAssoc) {
            $value = $model->__get($attributeName);
            if (!is_string($value)) {
                if ($isAssoc) $value = array_values($value);
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                $model->__set($attributeName, $value);
            }
        }
    }

    /**
     * Prepares JSON parameters
     * @param $event
     */
    public function initJson($event)
    {
        /**
         * @var $model \yii\db\ActiveRecord
         */
        $model = $this->owner;

        foreach ($this->fields as $attributeName => $isAssoc) {
            $value = $model->__get($attributeName);
            if (is_null($value) && $isAssoc) {
                $model->__set($attributeName, []);
            }
        }
    }

    /**
     * Converts JSON string into parameter
     * @param $event
     */
    public function fromJson($event)
    {
        /**
         * @var $model \yii\db\ActiveRecord
         */
        $model = $this->owner;

        foreach ($this->fields as $attributeName => $isAssoc) {
            $value = $model->__get($attributeName);
            if (empty($value) && $isAssoc) {
                $model->__set($attributeName, []);
            } else if (is_string($value)) {
                $value = json_decode($value, $isAssoc);
                $model->__set($attributeName, $value);
                $model->setOldAttribute($attributeName, $value);
            }
        }
    }
}

