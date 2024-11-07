<?php

namespace common\models;

use base\behaviors\JsonBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "sys_media".
 *
 * @property integer $id
 * @property string  $path
 * @property integer $object_id
 * @property string  $temp_id
 * @property integer $ordering
 * @property string  $category
 * @property string  $mediaType
 * @property string  $caption
 * @property integer $status
 * @property integer $created
 * @property integer $creator_id
 *
 * @property Users $creator
 */
class MediaFile extends \yii\db\ActiveRecord
{
//    /**
//     * {@inheritdoc}
//     */
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => \common\behaviors\JsonBehavior::class,
//                'fields' => [
//                    'meta_data' => true,
//                ]
//            ],
//        ];
//    }

    /**
     * Remove all model images
     */
    public static function deleteFiles($category, $object_id)
    {
        $images = MediaFile::getFiles($category, $object_id);
        if (count($images) < 1) {
            return true;
        } else {
            foreach ($images as $image) {
                $image->delete();
            }
        }
    }

    public static function getFile($id, $category, $object_id, $temp_id)
    {
        $imageQuery = MediaFile::find();
        $imageQuery->where([
            'id'       => $id,
            'category' => $category,
        ]);

        if($object_id && $temp_id) $imageQuery->andWhere("`object_id` = :object_id OR `temp_id` = :temp_id", ['object_id' => $object_id, 'temp_id' => $temp_id]);
        else if($temp_id) $imageQuery->andWhere("temp_id` = :temp_id", ['temp_id' => $temp_id]);
        else $imageQuery->andWhere("`object_id` = :object_id", ['object_id' => $object_id]);

        return $imageQuery->one();
    }

    public static function getNextOrder($category, $object_id)
    {
        $mediaQuery = MediaFile::find();
        $mediaQuery->where([
            'category'   => $category,
            'is_deleted' => 0,
            'object_id'  => $object_id,
        ]);
        $data = $mediaQuery->select('MAX(`ordering`) as max')->asArray(true)->one();
        $i = (int)$data['max'];
        return $i + 1;
    }

    public static function orderFiles($category, $object_id, $ordering)
    {
        $mediaQuery = MediaFile::find();
        $mediaQuery->where([
            'category'   => $category,
            'is_deleted' => 0,
            'object_id'  => $object_id,
        ]);
        $done = true;
        /* @var MediaFile $mediaModel */
        foreach($mediaQuery->all() as $mediaModel) {
            if(isset($ordering[$mediaModel->id])) $mediaModel->ordering = (int)$ordering[$mediaModel->id];
            else $mediaModel->ordering = 0;
            if(!$mediaModel->save()) {
                $done = false;
                break;
            }
        }
        return $done;
    }

    public static function getFiles($category, $object_id, $temp_id = '')
    {
        $imageQuery = static::find();
        $imageQuery->where([
            'category'   => $category,
            'is_deleted' => 0,
        ]);

        if($object_id && $temp_id) $imageQuery->andWhere("`object_id` = :object_id OR `temp_id` = :temp_id", ['object_id' => $object_id, 'temp_id' => $temp_id]);
        else if($temp_id) $imageQuery->andWhere("`temp_id` = :temp_id", ['temp_id' => $temp_id]);
        else $imageQuery->andWhere("`object_id` = :object_id", ['object_id' => $object_id]);

        $imageQuery->orderBy(['ordering' => SORT_ASC, 'id' => SORT_ASC]);
        return $imageQuery->all();
    }

    public function delete()
    {
        if(Yii::$app->upload->softDelete) {
            $this->is_deleted = 1;
            $this->save();
            return true;
        }

        $this->clearCache();

        $res = parent::delete();

        $storePath = Yii::$app->upload->getStorePath();
        $fileToRemove = $storePath . DIRECTORY_SEPARATOR . $this->path;
        if ($res && preg_match('@\.@', $fileToRemove) and is_file($fileToRemove)) {
            unlink($fileToRemove);
        }

        return $res;
    }

    public function clearCache(){
        $cachePath = Yii::$app->upload->getCachePath();
        $subdir = $this->category . '/'. $this->object_id;

        $dirToRemove = $cachePath . '/' . $subdir;

        if (preg_match('/' . preg_quote($cachePath, '/') . '/', $dirToRemove)) {
            \yii\helpers\BaseFileHelper::removeDirectory($dirToRemove);
            //exec('rm -rf ' . $dirToRemove);
            return true;
        } else {
            return false;
        }
    }

    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function getPathToOrigin()
    {
        $base = Yii::$app->upload->getStorePath();
        $path = $base.DIRECTORY_SEPARATOR.$this->path;
        return $path;
    }

    public function getUrl($size = false)
    {
        if(!$this->isImage() || (!Yii::$app->upload->hiddenPaths && !$size)) {
            return Yii::$app->upload->getStoreUrl() . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $this->path);
        }

        $this->getPath($size);

        if(!Yii::$app->upload->hiddenPaths) {
            return Yii::$app->upload->getCacheUrl() . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $this->versionPath($size));
        }

        $params = [
            '/site/media',
            'id'   => $this->id,
            //'media' => $this->versionPath($size),
        ];
        if($size) {
            $params['size'] = $size;
        }
        //$info = pathinfo($this->path);
        //$params['ext'] = $info['extension'];

        $url = Url::toRoute($params);
        return $url;
    }

    public function getPath($size = false){
        $origin = $this->getPathToOrigin();

        if(!$this->isImage() || $size === false) return $origin;

        $path = Yii::$app->upload->getCachePath() . DIRECTORY_SEPARATOR . $this->versionPath($size);
        if(!file_exists($path)){
            $this->createVersion($origin, $size);

            if(!file_exists($path)){
                //throw new \Exception('Problem with image creating.');
                return '';
            }
        }

        return $path;
    }

    public function getSizes()
    {
        $sizes = false;
        if(Yii::$app->upload->graphicsLibrary == 'Imagick'){
            $image = new \Imagick($this->getPathToOrigin());
            $sizes = $image->getImageGeometry();
        }else{
            $image = new \claviska\SimpleImage($this->getPathToOrigin());
            $sizes['width'] = $image->getWidth();
            $sizes['height'] = $image->getHeight();
        }

        return $sizes;
    }

    protected function versionPath($sizeString = false)
    {
        if($sizeString){
            $sizePart = '_'.$sizeString;
        }else{
            $sizePart = '';
        }

        $filename = pathinfo($this->path, PATHINFO_FILENAME);

        return
            $this->category . DIRECTORY_SEPARATOR .
            $this->object_id . DIRECTORY_SEPARATOR .
            $filename . $sizePart . '.' . pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function isImage()
    {
//        $info = pathinfo($this->path);
//        $ext = strtolower($info['extension'] ?? '');
//        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif']);
        $mime = BaseFileHelper::getMimeTypeByExtension($this->path);
        return (substr($mime, 0, 6) == 'image/');
    }

    public function createVersion($imagePath, $sizeString = false)
    {
        if(!file_exists($this->getPathToOrigin())) return;

        $pathToSave = Yii::$app->upload->getCachePath() . DIRECTORY_SEPARATOR . $this->versionPath($sizeString);

        BaseFileHelper::createDirectory(dirname($pathToSave), 0777, true);

        if($sizeString) {
            $size = Yii::$app->upload->parseSize($sizeString);
        } else {
            $size = false;
        }

        $realSize = $this->getSizes();

            if(Yii::$app->upload->graphicsLibrary == 'Imagick') {
                $image = new \Imagick($imagePath);
                $image->setImageCompressionQuality(100);

                if($size){
                    if($size['height'] && ($size['height'] < $realSize['height']) && $size['width'] && ($size['width'] < $realSize['width'])){
                        $image->cropThumbnailImage($size['width'], $size['height']);
                    }elseif($size['height'] && ($size['height'] < $realSize['height'])){
                        $image->thumbnailImage(0, $size['height']);
                    }elseif($size['width'] && ($size['width'] < $realSize['width'])){
                        $image->thumbnailImage($size['width'], 0);
                    }else{
                        //throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                    }
                }

                $image->writeImage($pathToSave);
            } else {

                $image = new \claviska\SimpleImage($imagePath);

                if($size){
                    if($size['height'] && $size['width'] && (($size['height'] > $realSize['height']) || ($size['width'] > $realSize['width']))) {
                        if (($size['height'] - $realSize['height']) > ($size['width'] - $realSize['width'])) {
                            $image->resize(null, $size['height']);
                            $image->thumbnail($size['width'], $size['height']);
                        } else {
                            $image->resize($size['width'], null);
                            $image->thumbnail($size['width'], $size['height']);
                        }
                    }elseif($size['height'] && ($size['height'] < $realSize['height']) && $size['width'] && ($size['width'] < $realSize['width'])){
                        $image->thumbnail($size['width'], $size['height']);
                    }elseif($size['height'] && ($size['height'] < $realSize['height'])){
                        $image->resize(null, $size['height']);
                    }elseif($size['width'] && ($size['width'] < $realSize['width'])){
                        $image->resize($size['width'], null);
                    }else{
                        //throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                    }
                }

                $image->toFile($pathToSave, null,100);
            }

        return $image;

    }

    public function save($runValidation = true, $attributeNames = null) {
        if(!$this->object_id && !$this->temp_id) return false;

        return parent::save($runValidation = true, $attributeNames = null);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path'/*, 'object_id'*/, 'category', 'caption'], 'required'],
            [['object_id', 'ordering'], 'integer'],
            [['ordering'], 'default', 'value' => 0],
            [['path'], 'string', 'max' => 400],
            [['caption'], 'string', 'max' => 200],
            [['category'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('admin', 'ID'),
            'path'   => Yii::t('admin', 'File Path'),
            'object_id'     => Yii::t('admin', 'Item ID'),
            'temp_id'     => Yii::t('admin', 'Temp ID'),
            'category'   => Yii::t('admin', 'Media Categorys'),
            'mediaType'  => Yii::t('admin', 'Media Type'),
            'urlAlias'   => Yii::t('admin', 'Url Alias'),
            'name'       => Yii::t('admin', 'Name'),
            'is_deleted' => Yii::t('admin', 'Is Deleted ?'),
            'created'    => Yii::t('admin', 'Created'),
            'creator_id' => Yii::t('admin', 'Creator'),
            'creator'    => Yii::t('admin', 'Creator'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    public static function copyFrom(MediaFile $src, ActiveRecord $model, $category = '')
    {
        // TODO MediaBehavior::uploadFile ??

        if (!$category) $category = $src->category;

        $absolutePath = $src->getPath();
        if (!file_exists($absolutePath)) return false;

        $copyData = static::_copyFile($absolutePath, $model, $category);
        if (!$copyData) return false;

        $ordering = $model->primaryKey ? MediaFile::getNextOrder($category, $model->primaryKey) : 0;

        /* @var MediaFile $image */
        $image = new MediaFile();
        $image->object_id = $model->primaryKey ?? 0;
        $image->temp_id = '';
        $image->path = $copyData['dir'] . DIRECTORY_SEPARATOR . $copyData['file'];
        $image->category = $category;
        $image->caption  = $copyData['file'];
        $image->creator_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id;
        $image->created = date('Y-m-d H:i:s');
        $image->ordering = $ordering;

        if(!$image->save()) return false;

        return $image;
    }

    protected static function _copyFile($file, ActiveRecord $model, $category)
    {
        if (!file_exists($file)) return false;

        $pi = pathinfo($file);
        $ext = $pi['extension'];
        $fileName = substr(md5(microtime(true) . $file), 4, 6) . '.' . $ext;

        $dir = $model->isNewRecord ? 'temp' :  $category . DIRECTORY_SEPARATOR . $model->primaryKey;
        $absoluteDir = Yii::$app->upload->getStorePath() . DIRECTORY_SEPARATOR . $dir;
        BaseFileHelper::createDirectory($absoluteDir, 0775, true);

        $absolutePath = $absoluteDir . DIRECTORY_SEPARATOR . $fileName;
        copy($file, $absolutePath);

        if (!file_exists($absolutePath)) return false;

        return ['dir' => $dir, 'file' => $fileName, 'absDir' => $absoluteDir, 'absFile' => $absolutePath];
    }

    /**
     * Converts object to string
     * @return string
     */
    public function __toString()
    {
        return $this->url;
    }
}
