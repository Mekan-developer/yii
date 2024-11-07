<?php

namespace common\components;

use common\models\MediaFile;
use yii\base\Component;
use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\Html;

class MediaUpload extends Component
{
    public $storePath       = '@app/web/upload/store';
    public $cachePath       = '@app/web/upload/cache';
    public $storeUrl        = '@web/upload/store';
    public $cacheUrl        = '@web/upload/cache';
    public $hiddenPaths     = false;
    public $graphicsLibrary = 'GD';
    public $previewSize     = '160x160';
    public $softDelete      = false;

    public function init()
    {
        parent::init();
    }

    public function getTempId() {
        $post = Yii::$app->request->post('fileTempId', '');
        if(!$post) {
            $userId = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id;
            $post = md5($userId . time());
        }
        return $post;
    }

    public function getUrl()
    {
        return \Yii::$app->request->hostInfo . \Yii::$app->request->url;
        //$path = Yii::$app->request->getPathInfo();
        //$id = Yii::$app->request->get('id');
        //if($id) $path .= "?id=" . $id;
        //return Url::to($path);
    }

    public function getMediaPreview($previewSize, MediaFile $file)
    {
        if(!$previewSize) $previewSize = $this->previewSize;

        $size = $this->parseSize($previewSize, true);
        if($file->isImage())
            return Html::img($file->getUrl($previewSize), ['class' => 'file-preview-image', 'alt' => $file->caption, 'title' => $file->caption]);
        else
            return Html::tag('div', Html::tag('div', '.' . $file->getExtension(), []), ['style' => 'width: ' . $size['width'] . 'px; height: ' . $size['height'] . 'px;', 'class' => 'mediaFilePreview']);
    }

    public function getMediaPreviewSimple($previewSize, $file)
    {
        if(!$previewSize) $previewSize = $this->previewSize;

        $size = $this->parseSize($previewSize, true);
        $filename = $this->getFilename($file);
        if($this->isImage($file))
            return Html::img($this->getFileUrl($file, $previewSize), ['class' => 'file-preview-image', 'alt' => $filename, 'title' => $filename]);
        else
            return Html::tag('div', Html::tag('div', '.' . $this->getExtension($file), []), ['style' => 'width: ' . $size['width'] . 'px; height: ' . $size['height'] . 'px;', 'class' => 'mediaFilePreview']);
    }

    public function getExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function getFilename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    public function isImage($path)
    {
        $mime = BaseFileHelper::getMimeTypeByExtension($path);
        return (substr($mime, 0, 6) == 'image/');
    }

    public function getFileUrl($path, $size = false)
    {
        if(!Yii::$app->upload->hiddenPaths && !$size) {
            return Yii::$app->upload->getStoreUrl() . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }

        //if(!Yii::$app->upload->hiddenPaths) {
            //$versionPath = $this->versionPath($path, $size);
            //$this->createVersion($path, $size);
            return Yii::$app->upload->getCacheUrl() . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $this->createVersion($path, $size));
        //}

        // TODO later ??
//
//        $params = [
//            '/site/media',
//            'id'   => $this->id,
//            //'media' => $this->versionPath($size),
//        ];
//        if($size) {
//            $params['size'] = $size;
//        }
//        //$info = pathinfo($this->path);
//        //$params['ext'] = $info['extension'];
//
//        $url = Url::toRoute($params);
//        return $url;
    }

    protected function versionPath($path, $sizeString = false, $create = true)
    {
        if($sizeString) {
            $sizePart = '_'.$sizeString;
        } else {
            $sizePart = '';
        }

        $info = pathinfo($path);

        return
            $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . $sizePart . '.' . $info['extension'];
    }

    public function createVersion($imagePath, $sizeString = false)
    {
        $versionPath = $this->versionPath($imagePath, $sizeString);
        $absDestPath = Yii::$app->upload->getCachePath() . DIRECTORY_SEPARATOR . $versionPath;
        if (file_exists($absDestPath)) {
            return $versionPath;
        }

        $absSrcPath = Yii::$app->upload->getStorePath() . DIRECTORY_SEPARATOR . $imagePath;
        if (!file_exists($absSrcPath)) {
            return '';
        }

        BaseFileHelper::createDirectory(dirname($absDestPath), 0777, true);

        if($sizeString) {
            $size = $this->parseSize($sizeString);
        } else {
            $size = false;
        }

        $realSize = $this->getSizes($absSrcPath);

        if($this->graphicsLibrary == 'Imagick') {
            $image = new \Imagick($absSrcPath);
            $image->setImageCompressionQuality(100);

            if($size){
                if ($size['height'] && ($size['height'] < $realSize['height']) && $size['width'] && ($size['width'] < $realSize['width'])) {
                    $image->cropThumbnailImage($size['width'], $size['height']);
                } elseif($size['height'] && ($size['height'] < $realSize['height'])) {
                    $image->thumbnailImage(0, $size['height']);
                } elseif($size['width'] && ($size['width'] < $realSize['width'])) {
                    $image->thumbnailImage($size['width'], 0);
                } else {
                    //throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                }
            }

            $image->writeImage($absDestPath);
        } else {

            $image = new \claviska\SimpleImage($absSrcPath);

            if($size){
                if ($size['height'] && ($size['height'] < $realSize['height']) && $size['width'] && ($size['width'] < $realSize['width'])) {
                    $image->thumbnail($size['width'], $size['height']);
                } elseif($size['height'] && ($size['height'] < $realSize['height'])) {
                    $image->resize(null, $size['height']);
                } elseif($size['width'] && ($size['width'] < $realSize['width'])) {
                    $image->resize($size['width'], null);
                } else {
                    //throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                }
            }

            $image->toFile($absDestPath, null,100);
        }

        return $versionPath;
    }

    public function getSizes($absPath)
    {
        $sizes = false;
        if ($this->graphicsLibrary == 'Imagick') {
            $image = new \Imagick($absPath);
            $sizes = $image->getImageGeometry();
        } else {
            $image = new \claviska\SimpleImage($absPath);
            $sizes['width'] = $image->getWidth();
            $sizes['height'] = $image->getHeight();
        }

        return $sizes;
    }

    public function getMediaPreviewConfigSimple($previewSize, $file, $model, $attribute)
    {
        if(!$previewSize) $previewSize = $this->previewSize;

        $size = $this->parseSize($previewSize, true);
        return (object)[
            'caption' => $this->getFilename($file),
            'width'   => $size['width'] . 'px',
            'height'  => $size['height'] . 'px',
            'url'     => $this->getUrl(),
            'extra'   => (object)[
                'file' => $file,
                'ajaxDelete' => 1,
                'attribute' => $attribute,
                'modelClass' => get_class($model),
                'modelKey'   => $model->getPrimaryKey(),
            ],
        ];
    }

    public function getMediaPreviewConfig($previewSize, $file, $model, $category, $pageUrl = '')
    {
        if(!$previewSize) $previewSize = $this->previewSize;

        $size = $this->parseSize($previewSize, true);
        return (object)[
            'caption' => $file->caption,
            'width'   => $size['width'] . 'px',
            'height'  => $size['height'] . 'px',
            'url'     => $pageUrl ? $pageUrl : $this->getUrl(),
            'key'     => $file->id,
            'extra'   => (object)[
                'id' => $file->id,
                'modelClass' => get_class($model),
                'ajaxDelete' => 1,
                'modelKey'   => $model->getPrimaryKey(),
                'fileTempId' => $this->getTempId(),
                'category'   => $category,
            ],
        ];
    }

    public function getStorePath()
    {
        return Yii::getAlias($this->storePath);
    }

    public function getStoreUrl()
    {
        return Yii::getAlias($this->storeUrl);
    }

    public function getCachePath()
    {
        return Yii::getAlias($this->cachePath);
    }

    public function getCacheUrl()
    {
        return Yii::getAlias($this->cacheUrl);
    }

    /**
     * Clear all images cache (and resized copies)
     * @return bool
     */
    public function clearImagesCache($category, $model)
    {
        $dirToRemove = $this->getCachePath() . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $model->primaryKey;
        \yii\helpers\BaseFileHelper::removeDirectory($dirToRemove);
        //exec('rm -rf ' . $dirToRemove);
        return true;
    }

    /**
     *
     * Creates size string
     * For instance: 400x400, 400x, x400
     *
     * @param $width
     * @param $height
     * @return string
     */
    public function createSize($width, $height)
    {
        return ($width ?: '') . 'x' . ($height ?: '');
    }

    /**
     *
     * Parses size string
     * For instance: 400x400, 400x, x400
     *
     * @param $notParsedSize
     * @return array|null
     */
    public function parseSize($notParsedSize, $notEmpty = false)
    {
        $sizeParts = explode('x', $notParsedSize);
        $part1 = (isset($sizeParts[0]) and $sizeParts[0] != '');
        $part2 = (isset($sizeParts[1]) and $sizeParts[1] != '');
        if ($part1 && $part2) {
            if (intval($sizeParts[0]) > 0
                &&
                intval($sizeParts[1]) > 0
            ) {
                $size = [
                    'width' => intval($sizeParts[0]),
                    'height' => intval($sizeParts[1])
                ];
            } else {
                $size = null;
            }
        } elseif ($part1 && !$part2) {
            $size = [
                'width' => intval($sizeParts[0]),
                'height' => $notEmpty ? intval($sizeParts[0]) :null,
            ];
        } elseif (!$part1 && $part2) {
            $size = [
                'width' => $notEmpty ? intval($sizeParts[1]) :null,
                'height' => intval($sizeParts[1])
            ];
        } else {
            throw new \Exception('Something bad with size, sorry!');
        }

        return $size;
    }
}