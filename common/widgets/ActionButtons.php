<?php

namespace common\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActionButtons extends \yii\base\Widget
{
    public $model            = NULL;
    public $defaultShowTitle = true;
    public $defaultAccess    = '';
    public $items            = [];
    public $options          = [];

    /**
     * Renders the widget.
     */
    public function run()
    {
        return $this->renderItems();
    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if(!isset($item['showTitle'])) $item['showTitle'] = $this->defaultShowTitle;
            if(!isset($item['access']))    $item['access'] = $this->defaultAccess;
            if(!isset($item['model']))     $item['model'] = $this->model;
            $item = $this->renderItem($item);
            if($item) $items[] = $item;
        }
        if(sizeof($items) == 0) return '';

        Html::addCssClass($this->options, ['widget' => 'actionButton-group']);
        return Html::tag('span', implode("\n", $items), $this->options);
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($item)
    {
        return ActionButton::widget($item);
    }
}
