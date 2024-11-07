<?php
namespace backend\widgets;

use backend\assets\TripServicesAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * @property \yii\db\ActiveRecord $model
 */
class TripServicesWidget extends Widget
{
    public $model = NULL;
    public $formName = NULL;
    public $services = NULL;
    public $options = [];
    public $disabled = false;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'trip-services']);

        $this->options['id'] = 'trip-services';

        if (empty($this->model) && empty($this->formName) && empty($this->services)) {
            throw new InvalidConfigException("The 'model' option is required OR 'formName' and 'services' attributes.");
        }
        if (!empty($this->model)) {
            $this->formName = $this->model->formName();
            $this->services = $this->model->servicesData;
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        TripServicesAsset::register($this->getView());


        $deleteStr = 'Удалить услугу';
        $formName = $this->formName;
        $html = Html::tag('script', '
        var tripServiceConfirm = "Удалить услугу из списка?";
        var tripServiceDelete = "' . $deleteStr . '";
        var tripServiceForm = "' . $formName . '[servicesData]";
        ') . Html::hiddenInput($formName . '[servicesData]', '');
        $body = '';

        $actions = '';
        $actions .= Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-trash"]), ['class' => 'btn btn-danger btn-sm removeButton', 'title' => $deleteStr]);
        foreach($this->services as $key => $data) {
            $id = $data['id'] ?? 0;
            $options = $this->disabled ? ['class' => 'form-control', 'disabled' => true, 'readonly' => true] : ['class' => 'form-control service-name', 'maxlength' => 50];
            if ($id) {
                $options['class'] .= ' disabled';
                $options['readonly'] = true;
            }
            $tmp = Html::tag('td', Html::hiddenInput($formName . '[servicesData][' . $key . '][id]', $id, ['class' => 'service-id']) . Html::textInput($formName . '[servicesData][' . $key . '][name]', $data['name'] ?? '', $options));
            $tmp .= Html::tag('td', Html::textInput($formName . '[servicesData][' . $key . '][price]', $data['price'] ?? 0, $this->disabled ? ['class' => 'form-control', 'disabled' => true, 'readonly' => true] : ['class' => 'form-control numeric0 service-price']));
            $tmp .= Html::tag('td', Html::textInput($formName . '[servicesData][' . $key . '][driver]', $data['driver'] ?? 0, $this->disabled ? ['class' => 'form-control', 'disabled' => true, 'readonly' => true] : ['class' => 'form-control numeric0 service-driver']));
            if (!$this->disabled) $tmp .= Html::tag('td', $actions, ['class' => 'gridActions', 'style' => 'vertical-align:middle;']);
            $body .= Html::tag('tr', $tmp, ['id' => 'trip-service' . $key]);
        }

        $create = Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-plus"]), ['class' => 'btn btn-success btn-sm addButton', 'data-id' => '1', 'title' => 'Добавить услугу']);

        $html .= Html::beginTag('table', ['cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%', 'class' => 'table table-bordered table-striped']);
        $head = Html::tag('th', 'Услуга', ['style' => 'vertical-align:middle;', 'width' => '70%']);
        $head .= Html::tag('th', 'Стоимость', ['style' => 'vertical-align:middle;', 'width' => '30%']);
        $head .= Html::tag('th', 'Водителю', ['style' => 'vertical-align:middle;', 'width' => '30%']);
        if (!$this->disabled) $head .= Html::tag('th', $create, ['width' => '1%']);
        $html .= Html::tag('thead', Html::tag('tr', $head));
        $html .= Html::tag('tbody', $body);
        $html .= Html::endTag('table');

        return Html::tag('div', $html, $this->options);
    }
}
