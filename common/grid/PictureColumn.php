<?php

namespace common\grid;

use yii\grid\DataColumn;
use yii\helpers\FileHelper;
use yii\helpers\Html;

// https://github.com/limion/yii2-bootstrap-media-lightbox

class PictureColumn extends DataColumn
{
    public $size = '100x100';
    public $sizeFull = false; //'800x';
    public $arrSize = null;
    public $count = 1;
    public $notImage = 'file &raquo;';

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        \base\assets\FuncyBoxAsset::register(\Yii::$app->getView());

        $this->format = 'raw';
        $value = $model->__get($this->attribute);
        $data = [];

        $this->initSize();

        /* @var \base\models\MediaFile $file */
        $i = 0;
        if(!is_array($value)) $value = [$value];
        foreach($value as $file) {
            if (!$file) continue;

            $options = [];
            if ($file->isImage()) {
                $url = $file->getUrl($this->size);
                $params = ['src' => $url, 'border' => 0, 'class' => 'img-thumbnail'];
                if(!empty($this->arrSize['height'])) $params['height'] = $this->arrSize['height'];
                if(!empty($this->arrSize['width'])) $params['width'] = $this->arrSize['width'];
                $img = Html::tag('img', '', $params);
            } else {
                $img = $this->notImage;
                $options['target'] = '_blank';
            }

            if($this->sizeFull !== false)
            {
                $img = Html::a($img, $file->getUrl($this->sizeFull)/* . '/' . $file->id . '.jpeg'*/, $options);
            }

            $data[] = $img;
            $i++;
            if($i >= $this->count) break;
        }

        $html = sizeof($data) == 0 ? '' : implode(' ', $data);
        if($this->sizeFull !== false && !empty($html)) $html = Html::tag('div', $html, ['class' => 'fancybox-media']);
        return empty($html) ? '&nbsp;' : $html;
    }

    protected function initSize()
    {
        if(is_array($this->arrSize)) return;
        $this->arrSize = \Yii::$app->upload->parseSize($this->size);
    }
}
