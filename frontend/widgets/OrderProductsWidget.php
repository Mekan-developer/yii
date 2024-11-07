<?php
namespace frontend\widgets;

use frontend\assets\OrderProductsWidgetAsset;
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
    public $useExistintgQ = true;

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
            $this->products = $this->model->setupProducts;
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
            $exists[$prodId] = $data;
        }

        $formName = $this->formName;
        $html = Html::hiddenInput($formName . '[setupProducts]', '');
        $body = '';
        $prodModel = new OrderProduct();

        $prodData = Product::getDataListFront();
        if (count($prodData[0]) == 0) {
            return Html::tag('div', '<h4 class="text-center text-danger">Извините, товар закончился.</h4>', $this->options);
        }

        foreach($prodData[0] as $prodId => $prodName) {
            $data = $exists[$prodId] ?? [];
            $prod = $prodData[1][$prodId] ?? [];

            $max = ($this->useExistintgQ ? ($data['q'] ?? 0) : 0) + ($prod['q'] ?? 0);
            $tmp = Html::tag('td',
                Html::hiddenInput($formName . '[setupProducts][' . $prodId . '][product_id]', $prodId, ['class' => 'product-id']) .
                ($data['name'] ?? $prodName) . (' (' . $max . ' шт.)'), ['valign' => 'middle', 'class' => 'product-name']);
            $tmp .= Html::tag('td', ($data['price'] ?? $prod['price']), ['valign' => 'middle', 'class' => 'product-price']);
            $tmp .= Html::tag('td', Html::textInput($formName . '[setupProducts][' . $prodId . '][q]', ($data['q'] ?? 0), $this->disabled ? ['class' => 'form-control product-q', 'disabled' => true, 'readonly' => true] : ['type' => 'number', 'step' => 1, 'min' => 0, 'max' => $max, 'class' => 'form-control numeric0 product-q', 'style' => 'min-width: 70px']) . '<small class="product-info">Макс: ' . $max . '</small>', ['class' => 'product-max-td']);
            $tmp .= Html::tag('td', ($data['sum'] ?? 0), ['valign' => 'middle', 'class' => 'product-sum']);
            $body .= Html::tag('tr', $tmp, ['id' => 'order-product-row' . $prodId, 'class' => 'product-id' . $prodId]);
        }

        $html .= Html::beginTag('table', ['cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%', 'class' => 'table table-bordered table-striped table-sm']);
        $head = Html::tag('th', $prodModel->getAttributeLabel('product_id'), ['style' => 'vertical-align:middle;', 'width' => '40%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('price'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('q'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        $head .= Html::tag('th', $prodModel->getAttributeLabel('sum'), ['style' => 'vertical-align:middle;', 'width' => '20%']);
        $html .= Html::tag('thead', Html::tag('tr', $head));
        $html .= Html::tag('tbody', $body);
        $html .= Html::endTag('table');

        return Html::tag('div', $html, $this->options);
    }
}
