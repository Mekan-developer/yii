<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\MediaFile;
use yii\helpers\BaseFileHelper;
use yii\helpers\ArrayHelper;

/**
 * Class MediaBehavior
 */
class MediaBehavior extends Behavior
{
    /**
     * Stores a list of relations, affected by the behavior. Configurable property.
     * @var array
     */
    public $media = [];

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT         => 'checkAjaxActions',
            ActiveRecord::EVENT_AFTER_FIND   => 'checkAjaxActions',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveMedia',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveMedia',
        ];
    }

    /**
     *
     * Method copies image file to module store and creates db record.
     *
     * @param $category
     * @param $absolutePath
     * @param bool $isFirst
     * @return bool|\base\models\MediaFile
     * @throws \Exception
     */
    protected function uploadFile($category, $image, $temp_id /*$isMain = false/*, $name = ''*/)
    {
        //var_dump($image->tempName, $image->baseName, $image->extension);die;
        //$path = Yii::getAlias('@webroot/upload/files/') . $image->baseName . '.' . $image->extension;

        $absolutePath = $image->tempName;

        if(!preg_match('#http#', $absolutePath)){
            if (!file_exists($absolutePath)) {
                throw new \Exception('File not exist! :'.$absolutePath);
            }
        } else {
            //nothing
        }

        /*if (!$this->owner->primaryKey) {
            throw new \Exception('Owner must have primaryKey when you attach image!');
        }*/

        $pictureFileName = substr(md5(microtime(true) . $absolutePath), 4, 6) . '.' . $image->extension;

        $newDir = $this->owner->isNewRecord ? 'temp' :  $category . DIRECTORY_SEPARATOR . $this->owner->primaryKey;
        $newAbsoluteDir = Yii::$app->upload->getStorePath() . DIRECTORY_SEPARATOR . $newDir;
        $newAbsolutePath = $newAbsoluteDir . DIRECTORY_SEPARATOR . $pictureFileName;
        BaseFileHelper::createDirectory($newAbsoluteDir, 0775, true);

        copy($absolutePath, $newAbsolutePath);

        if (!file_exists($newAbsolutePath)) {
            throw new \Exception('Cant copy file! ' . $absolutePath . ' to ' . $newAbsolutePath);
        }

        $ordering = $this->owner->primaryKey ? MediaFile::getNextOrder($category, $this->owner->primaryKey) : 0;

        /* @var MediaFile $image */
        $image = new MediaFile();
        $image->object_id = $this->owner->primaryKey ?: 0;
        $image->temp_id = (!$this->owner->primaryKey && $temp_id) ? $temp_id : '';
        $image->path = $newDir . DIRECTORY_SEPARATOR . $pictureFileName;
        $image->category = $category;
        $image->caption  = $pictureFileName;
        //$image->mediaType = substr(\yii\helpers\BaseFileHelper::getMimeTypeByExtension($pictureFileName), 0, 6) == 'image/' ? 'image' : 'other';
        //$image->name = $name;
        $image->creator_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id;
        $image->created = date('Y-m-d H:i:s');
        $image->ordering = $ordering;
        //$image->urlAlias = $this->getAlias($image);

        if(!$image->save()){
            return false;
        }

        if (count($image->getErrors()) > 0) {

            $ar = array_shift($image->getErrors());

            unlink($newAbsolutePath);
            throw new \Exception(array_shift($ar));
        }

        return $image;
    }

    protected function copyTempFile($category, MediaFile $file /*$image, $isMain = false/*, $name = ''*/)
    {
        $absolutePath = $file->getPathToOrigin();

        if(!preg_match('#http#', $absolutePath)){
            if (!file_exists($absolutePath)) {
                throw new \Exception('File not exist! :'.$absolutePath);
            }
        } else {
            //nothing
        }

        if (!$this->owner->primaryKey) {
            throw new \Exception('Owner must have primaryKey when you attach image!');
        }

        $info = pathinfo($file->path);
        $pictureFileName = $info["basename"];

        $newDir = $category . DIRECTORY_SEPARATOR . $this->owner->primaryKey;
        $newAbsoluteDir = Yii::$app->upload->getStorePath() . DIRECTORY_SEPARATOR . $newDir;
        $newAbsolutePath = $newAbsoluteDir . DIRECTORY_SEPARATOR . $pictureFileName;
        BaseFileHelper::createDirectory($newAbsoluteDir, 0775, true);

        copy($absolutePath, $newAbsolutePath);

        if (!file_exists($newAbsolutePath)) {
            throw new \Exception('Cant copy file! ' . $absolutePath . ' to ' . $newAbsolutePath);
        }
        unlink($absolutePath);

        $ordering = MediaFile::getNextOrder($category, $this->owner->primaryKey);

        $file->object_id = $this->owner->primaryKey;
        $file->temp_id = '';
        $file->ordering = $ordering;
        $file->path = $newDir . DIRECTORY_SEPARATOR . $pictureFileName;

        if(!$file->save()){
            return false;
        }

        if (count($file->getErrors()) > 0) {

            $ar = array_shift($file->getErrors());

            unlink($newAbsolutePath);
            throw new \Exception(array_shift($ar));
        }

        return $file;
    }

    public function checkAjaxActions($event)
    {
        $request = Yii::$app->getRequest();
        if(!$request->isConsoleRequest && $request->isAjax)
        {
            $eventObject = $event->sender;
            if(get_class($eventObject) == $request->post('modelClass', '') && (int)$eventObject->getPrimaryKey() == $request->post('modelKey', 0))
            {
                $eventCategory = $request->post('category', '');
                if (strpos($eventCategory, '[')) {
                    $simpleCategory = explode('[', $eventCategory);
                    $simpleCategory = array_shift($simpleCategory);
                } else {
                    $simpleCategory = $eventCategory;
                }
                foreach($this->media as $category => $mediaParams)
                {
                    if($category != $simpleCategory) continue;

                    // Upload
                    if($request->post('ajaxUpload', 0))
                    {
                        $this->uploadCategoryFiles($category, $mediaParams, true);

                        $result = [];
                        if(sizeof($this->_errors) > 0) {
                            $result['error'] = $this->_errors[0];
                            $result['errorkeys'] = $this->_errorsInFiles[$category];
                        }
                        $result['initialPreview'] = $this->_successPreview;
                        $result['initialPreviewConfig'] = $this->_successPreviewConfig;
                        echo json_encode( (object)$result );
                        die;
                    }

                    // Delete
                    $ajaxDelete = $request->post('ajaxDelete', 0);
                    if($ajaxDelete) {
                        $result = [];
                        $fileId = $request->post('id', 0);
                        $primaryModel = $this->owner;
                        if($fileId && ($file = MediaFile::getFile($fileId, $eventCategory, $primaryModel->getPrimaryKey(), Yii::$app->upload->getTempId())))
                        {
                            $file->delete();
                        } else {
                            $result['error'] = Yii::t('admin', 'File is not found.');
                        }
                        echo json_encode( (object)$result );
                        die;
                    }

                    // ajaxReordering
                    $ajaxReordering = $request->post('ajaxReordering', 0);
                    if($ajaxReordering) {
                        $ordering = (array)$request->post('ordering', []);
                        $primaryModel = $this->owner;
                        MediaFile::orderFiles($eventCategory, $primaryModel->getPrimaryKey(), array_flip($ordering));
                        die;
                    }
                }
            }
        }
    }

    /**
     * @param $event
     */
    public function saveMedia($event)
    {
        $request = Yii::$app->getRequest();
        if($request->isConsoleRequest) return;

        /**
         * @var $primaryModel \yii\db\ActiveRecord
         */
        foreach($this->media as $category => $mediaParams)
        {
            $this->uploadCategoryFiles($category, $mediaParams, false);

            $tmpImages = MediaFile::getFiles($category, 0, Yii::$app->upload->getTempId());
            foreach($tmpImages as $file) {
                $this->copyTempFile($category, $file);
            }
        }
    }

    public $_errors = [];
    public $_errorsInFiles = [];
    public $_successPreview = [];
    public $_successPreviewConfig = [];
    protected function uploadCategoryFiles($category, $mediaParams, $isAjax = false)
    {
        $primaryModel    = $this->owner;
        $attributeLabels = $primaryModel->attributeLabels();
        $attributeLabel  = isset($attributeLabels[$category]) ? $attributeLabels[$category] : $category;
        $previewSize = isset($mediaParams['previewSize']) ? (int)$mediaParams['previewSize'] : Yii::$app->upload->previewSize;
        $fileTempId = Yii::$app->upload->getTempId();

        $multiple = isset($mediaParams['multiple']) ? $mediaParams['multiple'] : false;
        if(!$multiple) {
            $language = $mediaParams['language'] ?? false;
            $proceed = [];
            if (!$language) {
                $proceed[] = $category;
            } else  {
                foreach(Yii::$app->i18n->languages as $lng => $lngInfo) {
                    $proceed[] = $category . '[' . $lng . ']';
                }
            }

            foreach($proceed as $proceedCategory) {
                $image = \yii\web\UploadedFile::getInstance($primaryModel, $proceedCategory);
                if($image) {
                    $existingImages = MediaFile::getFiles($proceedCategory, $this->owner->primaryKey, $fileTempId);
                    if (sizeof($existingImages) && $isAjax) {
                        $this->_errors[] = Yii::t('admin', 'File already uploaded. Delete it before uploading! {attribute}', ['attribute' => $attributeLabel]);
                        $this->_errorsInFiles[$proceedCategory][] = 0;
                    } else {
                        $image = $this->uploadFile($proceedCategory, $image, $fileTempId);
                        if ($image) {
                            foreach ($existingImages as $image) {
                                $image->delete();
                            }
                            $this->_successPreview[] = Yii::$app->upload->getMediaPreview($previewSize, $image);
                            $this->_successPreviewConfig[] = Yii::$app->upload->getMediaPreviewConfig($previewSize, $image, $primaryModel, $proceedCategory);
                        }
                    }
                }
            }
        } else {
            $images = \yii\web\UploadedFile::getInstances($primaryModel, $category);
            if(is_array($images) && sizeof($images))
            {
                $existingImages = MediaFile::getFiles($category, $this->owner->primaryKey, $fileTempId);
                $max = is_bool($multiple) ? 0 : (int)$multiple;
                $cnt = sizeof($existingImages);
                $errorAdded = false;
                foreach($images as $index => $image)
                {
                    if(!$max || $max > $cnt) {
                        $image = $this->uploadFile($category, $image, $fileTempId);
                        $this->_successPreview[] = Yii::$app->upload->getMediaPreview($previewSize, $image);
                        $this->_successPreviewConfig[] = Yii::$app->upload->getMediaPreviewConfig($previewSize, $image, $primaryModel, $category);
                    } else {
                        if(!$errorAdded) {
                            $this->_errors[] = Yii::t('admin', 'Maximum number of files! Allowed for "{attribute}": {allowed}', ['attribute' => $attributeLabel, 'allowed' => $max]);
                            $errorAdded = true;
                        }
                        $this->_errorsInFiles[$category][] = $index;
                    }
                    $cnt++;
                }
            }
        }
    }

    /**
     * Returns a value indicating whether a property can be read.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return array_key_exists($name, $this->media);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars and $checkBehaviors ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @param boolean $checkBehaviors whether to treat behaviors' properties as properties of this component
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return false;
    }

    /**
     * Returns the value of an object property.
     * Get it from our local temporary variable if we have it,
     * get if from DB otherwise.
     *
     * @param string $name the property name
     * @return mixed the property value
     * @see __set()
     */
    public function __get($name)
    {
        $params = $this->media[$name];
        $multiple = isset($params['multiple']) ? $params['multiple'] : false;
        $language = isset($params['language']) ? $params['language'] : false;
        if ($language) $lngName = $name . '[' . Yii::$app->language . ']';
        else $lngName = $name;
        $files = MediaFile::getFiles($lngName, $this->owner->getPrimaryKey());
        if(!sizeof($files))
        {
            if(isset($params['placeholder'])) {

                $options = $params['placeholder'];
                if(!is_array($options)) {
                    $options = ['file' => $options];
                }
                if (!isset($options['class'])) {
                    $options['class'] = \system\models\MediaFilePlaceholder::className();
                }
                $options['category'] = $name;
                $placeholder = Yii::createObject($options);
                return $multiple ? [$placeholder] : $placeholder;
            }
        }

        if(!$multiple) {
            if(sizeof($files)) return $files[0];
            return NULL;
        }

        return $files;
    }

    /**
     * Sets the value of a component property. The data is passed
     *
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @see __get()
     */
    public function __set($name, $value)
    {

    }
}

