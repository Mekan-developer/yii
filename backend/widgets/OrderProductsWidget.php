<?php
namespace backend\widgets;

use backend\assets\OrderProductsWidgetAsset;
use common\models\OrderProduct;
use common\models\Product;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * @property \yii\db\ActiveRecord $model
 */
class OrderProductsWidget extends Widget
{
    public $model = NULL;
    public $formName = NULL;
    public $products = NULL;
    public $options = [];
    public $disabled = false;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'order-products']);

        $this->options['id'] = 'order-products';

        if (empty($this->model) && empty($this->formName) && empty($this->services)) {
            throw new InvalidConfigException("The 'model' option is required OR 'formName' and 'services' attributes.");
        }
        if (!empty($this->model)) {
            $this->formName = $this->model->formName();
            $this->products = $this->model->editProducts;
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        OrderProductsWidgetAsset::register($this->getView());

        $exists = [];
        foreach($this->products as $key => $data) {
            $prodId = $data['product_id'] ?? '';
            $q = (int)($data['q'] ?? 0);
            if (!$prodId || !$q) continue;
            $exists[$prodId] = $q;
        }
        $deleteStr = 'Удалить товар';
        $formName = $this->formName;
        $html = Html::tag('script', '
        var orderProductConfirm = "Удалить товар из списка?";
        var orderProductDelete = "' . $deleteStr . '";
        var orderProductForm = "' . $formName . '[editProducts]";
        var orderProductsRows = ' . json_encode($exists) . ';
        ') . Html::hiddenInput($formName . '[editProducts]', '');
        $body = '';
        $prodModel = new OrderProduct();

        $actions = '';
        $actions .= Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-trash"]), ['class' => 'btn btn-danger btn-sm removeButton', 'title' => $deleteStr]);
        foreach($this->products as $key => $data) {
            $prod = Product::findOne($data['product_id'] ?? '');
            $max = ($prod ? $prod->q : 0) + ($data['q'] ?? 0);
            $tmp = Html::tag('td',
                Html::hiddenInput($formName . '[editProducts][' . $key . '][product_id]', $data['product_id'] ?? '', ['class' => 'product-id']) .
                Html::textInput($formName . '[editProducts][' . $key . '][name]', ($data['name'] ?? '') . (' (' . $max . ' шт.)'), ['class' => 'form-control product-name', 'disabled' => true, 'readonly' => true]));
            $tmp .= Html::tag('td', Html::textInput($formName . '[editProducts][' . $key . '][price]', $data['price'] ?? 0, ['class' => 'form-control product-price', 'disabled' => true, 'readonly' => true]));
            $tmp .= Html::tag('td', Html::textInput($formName . '[editProducts][' . $key . '][q]', $data['q'] ?? 0, $this->disabled ? ['class' => 'form-control product-q', 'disabled' => true, 'readonly' => true] : ['type' => 'number', 'step' => 1, 'min' => 0, 'max' => $max, 'class' => 'form-control numeric0 product-q']) . '<small class="product-info">Макс: ' . $max . '</small>', ['class' => 'product-max-td']);
            $tmp .= Html::tag('td', Html::textInput($formName . '[editProducts][' . $key . '][sum]', $data['sum'] ?? 0, ['class' => 'form-control product-sum', 'disabled' => true, 'readonly' => true]));
            if (!$this->disabled) $tmp .= Html::tag('td', $actions, ['class' => 'gridActions', 'style' => 'vertical-align:middle;']);
            $body .= Html::tag('tr', $tmp, ['id' => 'order-product-row' . $key, 'class' => 'product-id' . ($data['product_id'] ?? '')]);
        }

        $create = Html::tag('span', Html::tag('span', '', ["class"=> "fa fa-plus"]), ['class' => 'btn btn-success btn-sm addButton', 'data-id' => '1', 'title' => 'Добавить услугу']);

        $html .= Html::beginTag('table', ['cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%', 'class' => 'table table-bordered table-striped']);
        $head = Html::tag('th', $prodModel->getAttributeLabel('product_id'), ['style' => 'vertical-align:middle;', 'width' => '39%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('price'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('q'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('sum'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        if (!$this->disabled) $head .= Html::tag('th', $create, ['width' => '1%']);
        $html .= Html::tag('thead', Html::tag('tr', $head));
        $html .= Html::tag('tbody', $body);
        $html .= Html::endTag('table');

        return Html::tag('div', $html, $this->options);
    }
}
