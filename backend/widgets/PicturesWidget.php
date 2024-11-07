<?php
namespace backend\widgets;

use backend\assets\ChildrenAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * @property \yii\db\ActiveRecord $model
 */
class PicturesWidget extends Widget
{
    public $model = NULL;
    public $fileTypes = [];
    public $options = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        \common\assets\FuncyBoxAsset::register(\Yii::$app->view);

        parent::init();
        Html::addCssClass($this->options, ['widget' => 'pictures']);

        $this->options['id'] = 'pictures';

        if (empty($this->model)) {
            throw new InvalidConfigException("The 'model' option is required.");
        }

        if (empty($this->fileTypes)) {
            throw new InvalidConfigException("The 'fileTypes' option is required.");
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $html = '';
        foreach ($this->fileTypes as $fileType) {
            $files = $this->model->{$fileType};
            if (!is_array($files) || count($files) == 0) continue;
            $body = '';
            foreach($files as $file) {
                if (!$file) continue;

                $options = [];
                if ($file->isImage()) {
                    $url = $file->getUrl('100x');
                    $params = ['src' => $url, 'border' => 0, 'class' => 'img-thumbnail'];
                    $params['width'] = '100';
                    $img = Html::tag('img', '', $params);
                } else {
                    continue;
                }

                $img = Html::a($img, $file->getUrl(), $options);

                $body .= $img;
            }

            $html .= '<div class="m-2"><h6 class="fw-bold mb-3">' . $this->model->getAttributeLabel($fileType) . '</h6><div class="fancybox-media">' . $body . '</div></div>';
        }

        $this->options['class'] = 'd-flex flex-row';
        return Html::tag('div', $html, $this->options);
    }
}
