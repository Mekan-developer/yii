<?php

namespace common\grid;

use common\rbac\Access;
use Yii;
use Closure;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\Column;

class ActionMenuColumn extends Column
{
    public $defaultShowTitle = TRUE;
    public $defaultClass     = 'btn btn-primary';
    public $contentOptions   = ['class' => 'gridActions'];
    public $icon             = 'mdi mdi-dots-horizontal font-size-18';

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;

    /**
     * @var array button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * function ($url, $model, $key) {
     *     // return the button HTML code
     * }
     * ```
     *
     * where `$url` is the URL that the column creates for the button, `$model` is the model object
     * being rendered for the current row, and `$key` is the key of the model in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the model is
     * editable (here assuming you have a status field that indicates that):
     *
     * ```php
     * [
     *     'update' => function ($url, $model, $key) {
     *         return $model->status === 'editable' ? Html::a('Update', $url) : '';
     *     },
     * ],
     * ```
     */
    public $buttons = [];
    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;

    public $defaultAccess = '';
    /**
     * @var array html options to be applied to the [[initDefaultButtons()|default buttons]].
     * @since 2.0.4
     */
    public $buttonOptions = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @param array $saveGet save GET parameters
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index, $saveGet = [])
    {
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $saveGet);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            foreach($saveGet as $key) {
                $value = Yii::$app->request->get($key);
                if ($value) $params[$key] = $value;
            }
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return $params;//Url::toRoute($params);
        }
    }

    protected $_sep = '';
    protected function renderSeparator($name, $button, $model, $key, $index)
    {
        $this->_sep = '<div class="dropdown-divider"></div>';
        return '';
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

    protected function renderArrayButton($name, $button, $model, $key, $index)
    {
        if(is_callable($button))
        {
            $url = !empty($button['url']) ? $button['url'] : $this->createUrl($name, $model, $key, $index);
            if(!$this->actionAllowed($name, $url, $model)) return '';

            return ' '.call_user_func($button, $url, $model, $key);
        }
        else if(is_array($button))
        {
            if(sizeof($button) == 0)
            {
                return $this->renderSeparator($name, $button, $model, $key, $index);
            }

            $url = !empty($button['url']) ? $button['url'] : $this->createUrl($name, $model, $key, $index, @$button['saveGet'] ?: []);
            if(!$this->actionAllowed($name, $url, $model, isset($button['access']) ? $button['access'] : null )) return '';

            $icon      = ArrayHelper::getValue($button, 'icon', '');
            $title     = ArrayHelper::getValue($button, 'title', $name);
            $showTitle = ArrayHelper::getValue($button, 'showTitle', $this->defaultShowTitle);
            $confirm   = ArrayHelper::getValue($button, 'confirm', false);
            $isPost    = ArrayHelper::getValue($button, 'isPost', false);
            $class     = ArrayHelper::getValue($button, 'class', '');

            $options = [
                'title'      => $title,
                'aria-label' => $title,
                'data-pjax'  => '0',
                'class' => $class,
            ];
            Html::addCssClass($options, ['widget' => 'dropdown-item']);
            if($confirm) $options['data-confirm'] = $confirm;
            if($isPost)  $options['data-method'] = 'post';

            $content = '';
            if($icon)
            {
                $iconOptions = [];
                Html::addCssClass($iconOptions, ['widget' => 'actionIcon']);
                Html::addCssClass($iconOptions, $icon);
                $content .= Html::tag('span', '', $iconOptions);
            }

            if($showTitle) $content .= '&nbsp;&nbsp;' . Html::tag('span', $title, ['class' => 'buttonTitle']);
            $a = Html::a($content, $url, $options);

            $html = $this->_sep . ' ' . $a;
            $this->_sep = '';
            return $html;
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $this->_sep = '';

        $html = '';
        foreach($this->buttons as $name => $button)
        {
            $html .= $this->renderArrayButton($name, $button, $model, $key, $index);
        }
        if (!$html) return '';

        return '<div class="btn-group">
<a class="text-muted" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="' . $this->icon . '"></i></a>
<ul class="dropdown-menu-list dropdown-menu dropdown-menu-right">
' . $html . '
</ul>
</div>';
    }
}
