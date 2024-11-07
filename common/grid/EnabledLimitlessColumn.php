<?php

namespace common\grid;

use common\helpers\Access;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;

class EnabledLimitlessColumn extends DataColumn
{
    public $action = '';
    public $inverse = false;
    public $confirm = false;
    public $isPost  = true;

    public $titleOn = '';
    public $titleOff = '';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $tmp = $value = $model->__get($this->attribute);

        $defaultTitle = $model->getAttributeLabel($this->attribute);
        if($tmp)
        {
            $title = $this->titleOn ? $this->titleOn : $defaultTitle;
            if($this->inverse) {
                $span = '<span class="switchery switchery-default" style="background-color: rgb(239, 83, 80); border-color: rgb(239, 83, 80); box-shadow: rgb(239, 83, 80) 0px 0px 0px 10px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;"><small style="left: 18px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s; background-color: rgb(255, 255, 255);"></small></span>';
            } else {
                $span = '<span class="switchery switchery-default" style="background-color: rgb(100, 189, 99); border-color: rgb(100, 189, 99); box-shadow: rgb(100, 189, 99) 0px 0px 0px 10px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;"><small style="left: 18px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s; background-color: rgb(255, 255, 255);"></small></span>';
            }
        }
        else
        {
            $title = $this->titleOff ? $this->titleOff : $defaultTitle;
            $span = '<span class="switchery switchery-default" style="box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; border-color: rgb(223, 223, 223); background-color: rgb(255, 255, 255); transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;"><small style="left: 0px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small></span>';
        }

        if(!$this->action || !\Yii::$app->user->can(Access::actionByUrl([$this->action]))) return $span;
        $options = [
            'title'      => $title,
            'aria-label' => $title,
            'data-pjax'  => '0',
        ];
        if($this->confirm) $options['data-confirm'] = $this->confirm;
        if($this->isPost)  $options['data-method'] = 'post';

        return Html::a($span, $this->createUrl($this->action, $model, $key, $index), $options);
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        $params = is_array($key) ? $key : ['id' => (string) $key];
        $params[0] = $action;

        return Url::toRoute($params);
    }
}
