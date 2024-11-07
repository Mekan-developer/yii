<?php

namespace common\behaviors;

use yii\base\Behavior;

/**
 * Class Json2Behavior
 */
class Json3Behavior extends Behavior
{
    /**
     * Stores a list of fields, affected by the behavior.
     * @var array
     */
    public $fields = [];

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [];
    }

    /**
     * Returns a value indicating whether a property can be read.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return array_key_exists($name, $this->fields) ?
            true : parent::canGetProperty($name, $checkVars);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars and $checkBehaviors ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @param boolean $checkBehaviors whether to treat behaviors' properties as properties of this component
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return array_key_exists($name, $this->fields) ?
            true : parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * Returns the value of an object property.
     * Get it from our local temporary variable if we have it,
     * get if from DB otherwise.
     *
     * @param string $name the property name
     * @return mixed the property value
     * @see __set()
     */
    public function __get($name)
    {
        if (!isset($this->fields[$name])) return null;
        $key = $this->fields[$name];
        $value = $this->owner->__get($key);
        try {
            $value = json_decode($value, true);
        } catch (\Exception $e) {}
        if (!is_array($value)) $value = [];
        return $value;
    }

    /**
     * Sets the value of a component property. The data is passed
     *
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @see __get()
     */
    public function __set($name, $value)
    {
        if (!isset($this->fields[$name])) return null;
        $key = $this->fields[$name];
        $this->owner->__set($key, json_encode($value, JSON_UNESCAPED_UNICODE));
    }
}

