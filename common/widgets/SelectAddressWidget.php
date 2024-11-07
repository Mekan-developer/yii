<?php

namespace common\widgets;

use backend\assets\TripAddressesAsset;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * Widget for select map location. It\'s render google map and input field for type a map location.
 * Latitude and longitude are provided in the attributes $attributeLatitude and $attributeLongitude.
 * Add variables to define center map position as default
 * Base usage:
 *
 * $form->field($model, 'location')->widget(\app\widgets\SelectMapLocationWidget::className(), [
 *     'attributeLatitude' => 'latitude',
 *     'attributeLongitude' => 'longitude',
 * ]);
 *
 * or
 *
 * \app\widgets\SelectMapLocationWidget::widget([
 *     'model' => $model,
 *     'attribute' => 'location',
 *     'attributeLatitude' => 'latitude',
 *     'attributeLongitude' => 'longitude',
 * ]);
 *
 * @author Max Kalyabin <maksim@kalyabin.ru>
 * @package yii2-select-google-map-location
 * @copyright (c) 2015, Max Kalyabin, http://github.com/kalyabin
 *
 * @property Model $model base yii2 model or ActiveRecord object
 * @property string $attribute attribute to write map location
 * @property string $attributeLatitude attribute to write location latitude
 * @property string $attributeLongitude attribute to write location longitude
 * @property callable|null $renderWidgetMap custom function to render map
 */
class SelectAddressWidget extends InputWidget
{
    /**
     * @var string latitude attribute name
     */
    public $attributeLatitude;

    /**
     * @var string longitude attribute name
     */
    public $attributeLongitude;

    public $plusIcon = false;

    /**
     * @var array options for attribute text input
     */
    public $textOptions = ['class' => 'form-control'];

    /**
     * @var array JavaScript options
     */
    public $jsOptions = [];

    /**
     * Run widget
     */
    public function run()
    {
        parent::run();

        TripAddressesAsset::register($this->view);

        $jsOptions = ArrayHelper::merge($this->jsOptions, [
            'address'           => '#' . $this->attribute,
            'latitude'          => '#' . $this->attributeLatitude,
            'longitude'         => '#' . $this->attributeLongitude,
            'defaultLatitude'   => '48.474742',
            'defaultLongitude'  => '135.076337',
        ]);
        $id = $this->attribute . '_wrap';
        $this->view->registerJsVar('selectAddressAutocomplete', Url::to(['/site/address-autocomplete']));
        $this->view->registerJs(new JsExpression('$(\'#' . $id . '\').selectAddress(' . Json::encode($jsOptions) . ');'));
        $this->field->options['id'] = $id;
        $this->textOptions['id'] = $this->attribute;
        $this->field->template = preg_replace('/\{input\}/', '<div class="input-group">{input}' . ($this->plusIcon ? '<button type="button" class="btn btn-success btn-addradd"><i class="fas fa-plus"></i></button>' : '') . '</div>', $this->field->template);

        $this->textOptions['readonly'] = true;
        return Html::activeInput('text', $this->model, $this->attribute, $this->textOptions) .
                    Html::activeHiddenInput($this->model, $this->attributeLatitude, ['id' => $this->attributeLatitude]) .
                    Html::activeHiddenInput($this->model, $this->attributeLongitude, ['id' => $this->attributeLongitude]);
    }
}
