<?php
namespace backend\widgets;

use backend\assets\ChildrenAsset;
use common\assets\AirDatepicker3Asset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\MaskedInputAsset;

/**
 * @property \yii\db\ActiveRecord $model
 */
class ChildrenWidget extends Widget
{
    public $model = NULL;
    public $options = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'children']);

        $this->options['id'] = 'children';

        if (empty($this->model)) {
            throw new InvalidConfigException("The 'model' option is required.");
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        ChildrenAsset::register($this->getView());
        MaskedInputAsset::register($this->getView());
        AirDatepicker3Asset::register($this->getView());

        $deleteStr = 'Удалить имя ребёнка';
        $formName = $this->model->formName();
        $html = Html::tag('script', '
        var childrenConfirm = "Удалить имя ребёнка из списка?";
        var childrenDelete = "' . $deleteStr . '";
        var childrenForm = "' . $formName . '[childrenData]";
        ');
        $body = '';

        $actions = '';
        $body .= Html::hiddenInput($formName . '[childrenData]', '');
        $actions .= Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-trash"]), ['class' => 'btn btn-danger btn-sm removeButton', 'title' => $deleteStr]);
        foreach($this->model->childrenData as $key => $data) {
            $birthDate = $birthDateSql = $name = '';
            if (!is_array($data)) {
                $name = $data;
            } else {
                $name = $data[0] ?? '';
                $birthDate = $birthDateSql = $data[1] ?? '';
                if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $birthDate, $t)) {
                    $birthDateSql = $t[3] . '-' . $t[2] . '-' . $t[1];
                }
            }

            $tmp = Html::tag('td', Html::textInput($formName . '[childrenData][' . $key . '][0]', $name, ['class' => 'form-control', 'maxlength' => 25]));
            $tmp .= Html::tag('td', Html::textInput($formName . '[childrenData][' . $key . '][1]', $birthDate, ['class' => 'form-control bdate', 'data-inputmask-mask' => '99.99.9999', 'data-date' => $birthDateSql, 'maxlength' => 25]));
            $tmp .= Html::tag('td', $actions, ['class' => 'gridActions', 'style' => 'vertical-align:middle;']);
            $body .= Html::tag('tr', $tmp, ['id' => 'children' . $key]);
        }

        $create = Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-plus"]), ['class' => 'btn btn-success btn-sm addButton', 'data-id' => 1, 'title' => 'Добавить имя ребёнка']);

        $html .= Html::beginTag('table', ['cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%', 'class' => 'table table-bordered table-striped']);
        $head = Html::tag('th', 'Имя ребёнка', ['style' => 'vertical-align:middle;', 'width' => '48%']);
        $head .= Html::tag('th', 'День рождения', ['width' => '48%']);
        $head .= Html::tag('th', $create, ['width' => '1%']);
        $html .= Html::tag('thead', Html::tag('tr', $head));
        $html .= Html::tag('tbody', $body);
        $html .= Html::endTag('table');

        return Html::tag('div', $html, $this->options);
    }
}
