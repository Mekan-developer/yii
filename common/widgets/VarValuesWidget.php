<?php

namespace common\widgets;

use common\assets\VarValuesWidgetAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

/**
 * @property \yii\db\ActiveRecord $model
 */
class VarValuesWidget extends Widget
{
    public $model = NULL;
    public $options = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'var-values-rows']);

        $this->options['id'] = 'var-values-rows';

        if (empty($this->model)) {
            throw new InvalidConfigException("The 'model' option is required.");
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        VarValuesWidgetAsset::register($this->getView());

        $deleteStr = Yii::t('vars', 'Delete value');
        $html = Html::tag('script', '
        var varRowConfirm = "' . Yii::t('vars', 'Are you sure to remove it?') . '";
        var varRowDelete = "' . $deleteStr . '";
        var varRowForm = "' . $this->model->formName() . '[editValues]";
        ');

        $formName = $this->model->formName();

        $body = '';

        $actions = '';
        $actions .= Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-trash"]), ['class' => 'btn btn-danger btn-sm removeButton', 'title' => $deleteStr]);
        $key = 0;
        $values = $this->model->editValues;
        if (is_array($values)) {
            foreach ($values as $valueData) {
                $tmp = Html::tag('td', Html::textInput($formName . '[editValues][' . $key . '][value]', $valueData['value'], ['class' => 'form-control']));
                $tmp .= Html::tag('td', Html::textInput($formName . '[editValues][' . $key . '][name]', $valueData['name'], ['class' => 'form-control']));
                $tmp .= Html::tag('td', $actions, ['class' => 'gridActions', 'style' => 'vertical-align:middle;']);
                $body .= Html::tag('tr', $tmp, ['id' => 'varRow' . $key]);
                $key++;
            }
        }

        $create = Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-plus"]), ['class' => 'btn btn-success btn-sm addButton', 'data-id' => '1', 'title' => Yii::t('Yii', 'Add value')]);

        $html .= Html::hiddenInput($formName . '[editValues]', '');
        $html .= Html::beginTag('table', ['cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%', 'class' => 'table table-bordered table-striped']);
        $head = Html::tag('th', Yii::t('vars', 'Значение'), ['style' => 'vertical-align:middle;', 'width' => '50%']);
        $head .= Html::tag('th', Yii::t('vars', 'Описание'), ['style' => 'vertical-align:middle;', 'width' => '49%']);
        $head .= Html::tag('th', $create, ['width' => '1%']);
        $html .= Html::tag('thead', Html::tag('tr', $head));
        $html .= Html::tag('tbody', $body);
        $html .= Html::endTag('table');

        return Html::tag('div', $html, $this->options);
    }
}
