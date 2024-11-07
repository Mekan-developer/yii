<?php
namespace backend\widgets;

use common\assets\SelectAddressAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * @property \yii\db\ActiveRecord $model
 */
class TripAddressesWidget extends Widget
{
    public $model = NULL;
    public $options = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, ['widget' => 'trip-addresses']);

        SelectAddressAsset::register(\Yii::$app->view);

        $this->options['id'] = 'trip-addresses';

        if (empty($this->model)) {
            throw new InvalidConfigException("The 'model' option is required.");
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $formName = $this->model->formName();
        $html = '<input type="hidden" name="' . $formName . '[addressesData]" value="" />
                    <div id="addresses" data-id="1" data-form="' . $formName . '">';

        foreach($this->model->addressesData as $i => $addrData) {
            $html .= '<div class="form-group" id="addr' . $i . '"><div class="input-group">' .
                '<input type="text" id="address-addr' . $i . '" class="form-control addr-address" name="' . $formName . '[addressesData][' . $i . '][address]" readonly="" value="' . ($addrData['address'] ?? '') . '">' .
                '<input type="hidden" id="lat-addr' . $i . '" name="' . $formName . '[addressesData][' . $i . '][lat]" value="' . ($addrData['lat'] ?? '') . '" class="addr-lat">' .
                '<input type="hidden" id="lon-addr' . $i . '" name="' . $formName . '[addressesData][' . $i . '][lon]" value="' . ($addrData['lon'] ?? '') . '" class="addr-lon">' .
                '<button type="button" class="btn btn-danger btn-addremove"><i class="fas fa-minus"></i></button>' .
                '</div></div>';
        }

        $html .= '</div>';

        return Html::tag('div', $html, $this->options);
    }
}
