<?php

namespace common\validators;

use common\assets\ExtValidationAsset;
use Yii;

class RequiredFloatValidator extends \yii\validators\RequiredValidator
{
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->requiredValue === null) {
            //if ($this->strict && $value !== null || !$this->strict && !$this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $valueTest = is_string($value) ? trim($value) : $value;
            if ($this->strict && $value !== null || (!$this->strict && (float)$valueTest != 0)) {
                return null;
            }
        } elseif (!$this->strict && $value == $this->requiredValue || $this->strict && $value === $this->requiredValue) {
            return null;
        }

        if ($this->requiredValue === null) {
            return [$this->message, []];
        } else {
            return [$this->message, [
                'requiredValue' => $this->requiredValue,
            ]];
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $options = [];
        if ($this->requiredValue !== null) {
            $options['message'] = Yii::$app->getI18n()->format($this->message, [
                'requiredValue' => $this->requiredValue,
            ], Yii::$app->language);
            $options['requiredValue'] = $this->requiredValue;
        } else {
            $options['message'] = $this->message;
        }
        if ($this->strict) {
            $options['strict'] = 1;
        }

        $options['message'] = Yii::$app->getI18n()->format($options['message'], [
            'attribute' => $model->getAttributeLabel($attribute),
        ], Yii::$app->language);

        ExtValidationAsset::register($view);

        return 'yii.validationext.requiredNumeric(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}
