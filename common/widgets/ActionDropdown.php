<?php

namespace common\widgets;

use common\grid\GridModelActionsInterface;
use common\rbac\Access;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionDropdown extends \yii\base\Widget
{
    public $model            = NULL;
    public $defaultAccess    = '';
    public $items            = [];
    public $options          = [];
    public $icon             = 'mdi mdi-dots-horizontal font-size-18';
    public $menuClass        = 'dropdown-menu dropdown-menu-end';
    public $linkClass        = 'dropdown-item';

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
        $lastGroup = '';
        foreach ($this->items as $name => $item) {

            $group = $item['group'] ?? '';
            if ($group != $lastGroup) {
                if (count($items) > 0) $items[] = '<div class="dropdown-divider"></div>';
                $lastGroup = $group;
            }


            if ($name && $item) {
                $items[] = $this->renderItem($name, $item);
            }
        }
        if(sizeof($items) == 0) return '';

        $button = '<a class="text-muted" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="' . $this->icon . '"></i></a>';
        Html::addCssClass($this->options, ['widget' => 'dropdown']);
        $menu = Html::tag('div', implode("\n", $items), ['class' => $this->menuClass]);
        return Html::tag('div', $button . $menu, $this->options);
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($name, $item)
    {
        $url = !empty($item['url']) ? $item['url'] : [$name];
        if (is_array($url) && $this->model) {
            $url['id'] = $this->model->id;
        }
        $access = !empty($item['access']) ? $item['access'] : $this->defaultAccess;
        if (!$this->actionAllowed($name, $url, $this->model, $access)) {
            return '';
        }


        $icon      = ArrayHelper::getValue($item, 'icon', '');
        $title     = ArrayHelper::getValue($item, 'title', $name);
        $confirm   = ArrayHelper::getValue($item, 'confirm', false);
        $isPost    = ArrayHelper::getValue($item, 'isPost', false);
        $class     = ArrayHelper::getValue($item, 'class', '');

        $options = [
            'title'      => $title,
            'aria-label' => $title,
            'data-pjax'  => '0',
        ];
        if ($class) {
            $options['class'] = $class;
        }
        Html::addCssClass($options, ['widget' => $this->linkClass]);
        if ($confirm) {
            $options['data-confirm'] = $confirm;
        }
        if ($isPost) {
            $options['data-method'] = 'post';
        }

        $content = [];
        if ($icon) {
            $content[] = '<i class="' . $icon . '"></i>';
        }
        if ($title) {
            $content[] = $title;
        }
        $content = implode('&nbsp;', $content);

        return Html::a($content, Url::to($url), $options);
    }

    protected function actionAllowed($name, $url, $model, $access = null)
    {
        if ($access) {
            if (!Access::checkAccess($access, $url)) {
                return false;
            }
        } elseif ($this->defaultAccess) {
            if (!Access::checkAccess($this->defaultAccess, $url)) {
                return false;
            }
        }

        if ($model instanceof GridModelActionsInterface) {
            if (!$model->actionAllowed($name)) {
                return false;
            }
        }

        return true;
    }
}
