<?php

namespace common\grid;

use common\helpers\Access;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;

class EnabledColumn extends DataColumn
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

        if($this->inverse) $tmp = !$tmp;
        $defaultTitle = $model->getAttributeLabel($this->attribute);
        if($tmp)
        {
            $title = $this->titleOn ? $this->titleOn : $defaultTitle;
            $i = Html::tag('i', '', ['class' => 'fa fa-toggle-on']);
            $span = Html::tag('span', $i, ['class' => 'btn btn-sm btn-success']);
        }
        else
        {
            $title = $this->titleOff ? $this->titleOff : $defaultTitle;
            $i = Html::tag('i', '', ['class' => 'fa fa-toggle-off']);
            $span = Html::tag('span', $i, ['class' => 'btn btn-sm btn-danger']);
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
