<?php

namespace common\grid;

use common\rbac\Access;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;

class BooleanColumn extends DataColumn
{
    public $action = '';
    public $inverse = false;
    public $inverseColor = false;
    public $confirm = false;
    public $isPost  = true;

    public $titleOn = 'Да';
    public $titleOff = 'Нет';

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $tmp = $value = $model->__get($this->attribute);

        if($this->inverse) $tmp = !$tmp;
        if($tmp)
        {
            $title = $this->titleOn ?: 'Yes';
            $span = Html::tag('span', $title, ['class' => 'badge ' . ($this->inverseColor ? 'bg-danger' : 'bg-success')]);
        }
        else
        {
            $title = $this->titleOff ?: 'No';
            $span = Html::tag('span', $title, ['class' => 'badge ' . ($this->inverseColor ? 'bg-success' : 'bg-danger')]);
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
