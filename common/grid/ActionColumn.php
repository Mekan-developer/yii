<?php

namespace common\grid;

use Yii;
use Closure;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\Column;
use common\helpers\Access;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 *
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => ActionColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ActionColumn extends Column
{
    public $defaultShowTitle = false;
    public $defaultClass     = 'btn btn-primary btn-sm';
    public $contentOptions   = ['class' => 'gridActions'];
    public $noBrake          = true;

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;
    /**
     * @var string the template used for composing each cell in the action column.
     * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names*
     * in the context of action column). They will be replaced by the corresponding button rendering callbacks
     * specified in [[buttons]]. For example, the token `{view}` will be replaced by the result of
     * the callback `buttons['view']`. If a callback cannot be found, the token will be replaced with an empty string.
     *
     * As an example, to only have the view, and update button you can add the ActionColumn to your GridView columns as follows:
     *
     * ```
     * ['class' => 'app\grid\ActionColumn', 'template' => '{view} {update}'],
     * ```
     *
     * @see buttons
     */
    public $template = '';//'{view} {update} {delete}';
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

    public $defaultAccess = '$';
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
        $this->initDefaultButtons();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!empty($this->template)) {
            if (!isset($this->buttons['view'])) {
                $this->buttons['view'] = function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('admin', 'Просмотр'),
                        'aria-label' => Yii::t('admin', 'Просмотр'),
                        'data-pjax' => '0',
                    ], $this->buttonOptions);
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                };
            }
            if (!isset($this->buttons['update'])) {
                $this->buttons['update'] = function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('admin', 'Edit'),
                        'aria-label' => Yii::t('admin', 'Edit'),
                        'data-pjax' => '0',
                    ], $this->buttonOptions);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                };
            }
            if (!isset($this->buttons['delete'])) {
                $this->buttons['delete'] = function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('admin', 'Delete'),
                        'aria-label' => Yii::t('admin', 'Delete'),
                        'data-confirm' => Yii::t('admin', 'Are You sure to delete this record ?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ], $this->buttonOptions);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                };
            }
        }
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
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return $params;
        }
    }

    protected $_sep = '';
    protected function renderSeparator($name, $button, $model, $key, $index)
    {
        $this->_sep = ' ' . Html::tag('span', '', ['class' => 'actionSeparator']);
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
        if (is_callable($button)) {
            return ' ' . call_user_func($button, $model, $key);
        } elseif (is_array($button)) {
            if (sizeof($button) == 0) {
                return $this->renderSeparator($name, $button, $model, $key, $index);
            }

            $url = !empty($button['url']) ? $button['url'] : $this->createUrl($name, $model, $key, $index);
            if (!$this->actionAllowed($name, $url, $model, isset($button['access']) ? $button['access'] : null )) {
                return '';
            }

            $icon      = ArrayHelper::getValue($button, 'icon', '');
            $title     = ArrayHelper::getValue($button, 'title', $name);
            $showTitle = ArrayHelper::getValue($button, 'showTitle', $this->defaultShowTitle);
            $confirm   = ArrayHelper::getValue($button, 'confirm', false);
            $isPost    = ArrayHelper::getValue($button, 'isPost', false);
            $newWindow = ArrayHelper::getValue($button, 'newWindow', false);
            $class     = ArrayHelper::getValue($button, 'class', $this->defaultClass);

            $options = [
                'title'      => $title,
                'aria-label' => $title,
                'data-pjax'  => '0',
            ];
            if ($class) {
                $options['class'] = $class;
            }
            if ($confirm) {
                $options['data-confirm'] = $confirm;
            }
            if ($isPost) {
                $options['data-method'] = 'post';
            }
            if ($newWindow) {
                $options['target'] = '_blank';
            }
            $options = array_merge($this->buttonOptions, $options);

            $content = '';
            if ($icon) {
                $iconOptions = [];
                Html::addCssClass($iconOptions, ['widget' => 'actionIcon']);
                Html::addCssClass($iconOptions, $icon);
                $content .= Html::tag('span', '', $iconOptions);
            }

            if ($showTitle) {
                $content .= Html::tag('span', $title, ['class' => 'buttonTitle']);
            }

            $html = $this->_sep . ' ' . Html::a($content, $url, $options);
            $this->_sep = '';
            return $html;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $this->_sep = '';
        if (empty($this->template)) {
            $html = '';
            foreach ($this->buttons as $name => $button) {
                $html .= $this->renderArrayButton($name, $button, $model, $key, $index);
            }
            if (!$this->noBrake) {
                return $html;
            }
            return '<nobr>' . $html . '</nobr>';
        }

        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                return $this->renderArrayButton($name, $this->buttons[$name], $model, $key, $index);
            } else {
                return '';
            }
        }, $this->template);
    }
}
