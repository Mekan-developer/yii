<?php

namespace common\widgets;

use common\grid\GridModelActionsInterface;
use common\helpers\Access;
use Yii;
use yii\helpers\Html;

class ActionButton extends \yii\base\Widget
{
    public $name          = '';
    public $model         = NULL;
    public $title         = '';
    public $text          = '';
    public $iconClass     = '';
    public $icon          = '';
    public $permission    = '';
    public $showTitle     = true;
    public $options       = [];
    public $url           = '';
    public $access        = '';

    protected function actionAllowed()
    {
        if(empty($this->name) && !is_array($this->url)) return false;

        if($this->access) {
            if(!$this->url && $this->name) $this->url = [$this->name];
            if(!Access::checkAccess($this->access, $this->url)) return false;
        }

        if ($this->model instanceof GridModelActionsInterface) {
            if(!$this->model->actionAllowed($this->name)) return false;
        }

        return true;
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        if(!$this->actionAllowed()) return '';
        $content = '';

        if($this->iconClass)
        {
            $iconOptions = [];
            Html::addCssClass($iconOptions, ['widget' => 'actionIcon']);
            Html::addCssClass($iconOptions, $this->iconClass);
            $content .= Html::tag('span', '', $iconOptions);
        } else if ($this->icon) {
            $content .= $this->icon;
        }

        if($this->showTitle && $this->text) $content .= Html::tag('span', $this->text, ['class' => 'buttonTitle']);
        elseif($this->showTitle && $this->title) $content .= Html::tag('span', $this->title, ['class' => 'buttonTitle']);
        if(empty($this->options['class'])) $this->options['class'] = 'actionButton btn btn-primary btn-sm';
        else Html::addCssClass($this->options, ['widget' => 'actionButton']);
        $this->options['title'] = $this->title;
        if(!$this->url && $this->name) $this->url = [$this->name];
        return Html::a($content, $this->url, $this->options);
    }
}
