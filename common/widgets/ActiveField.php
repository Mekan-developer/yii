<?php

namespace common\widgets;

use common\assets\AirDatepicker3Asset;
use common\behaviors\MediaBehavior;
use common\models\MediaFile;
use common\theme\skote\assets\BootstrapDatepicker;
use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class ActiveField extends \yii\widgets\ActiveField
{
    public function textInputSimple($options = [])
    {
        $this->setupNoWrap($options);
        $this->template = '{input}';
        return parent::textInput($options);
    }

    public function dropDownListSimple($items, $options = [])
    {
        $this->setupNoWrap($options);
        $this->template = '{input}';
        return parent::dropDownList($items, $options);
    }

    public function textInputExt($options = [], $leftButtons = [], $rightButtons = [])
    {
        if ((is_array($leftButtons) && count($leftButtons) > 0) ||
            (is_array($rightButtons) && count($rightButtons) > 0)) {
            $left = implode('', $leftButtons);
            $right = implode('', $rightButtons);
            $this->template = str_replace('{input}', '<div class="input-group">' . $left . '{input}' . $right . '</div>', $this->template);
        }

        return parent::textInput($options);
    }

    public function dropDownListExt($items, $options = [], $leftButtons = [], $rightButtons = [])
    {
        if ((is_array($leftButtons) && count($leftButtons) > 0) ||
            (is_array($rightButtons) && count($rightButtons) > 0)) {
            $left = implode('', $leftButtons);
            $right = implode('', $rightButtons);
            $this->template = str_replace('{input}', '<div class="input-group">' . $left . '{input}' . $right . '</div>', $this->template);
        }

        return parent::dropDownList($items, $options);
    }

    public function textSubmodelInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);

        if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
            $this->addErrorClassIfNeeded($options);
        }

        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

        return $this;
    }

    public function extTextInput($options = [], $inputGroup = [])
    {
        $this->textInput($options);

        $prepend = $inputGroup['prepend'] ?? '';
        $append = $inputGroup['append'] ?? '';
        $groupId = $inputGroup['group-id'] ?? '';

        if (strlen($prepend) > 0 || strlen($append) > 0) {
            $groupOptions = $inputGroup['options'] ?? [];
            if ($groupId) {
                $groupOptions['id'] = $groupId;
            }
            if (!is_array($groupOptions)) $groupOptions = [];
            Html::addCssClass($groupOptions, ['widget' => 'input-group']);
            $this->parts['{input}'] = Html::tag('div', $prepend . $this->parts['{input}'] . $append, $groupOptions);
        }

        return $this;
    }

    public function extPasswordInput($options = [], $inputGroup = [])
    {
        $this->passwordInput($options);

        $prepend = $inputGroup['prepend'] ?? '';
        $append = $inputGroup['append'] ?? '';

        if (strlen($prepend) > 0 || strlen($append) > 0) {
            $groupOptions = $inputGroup['options'] ?? [];
            if (!is_array($groupOptions)) $groupOptions = [];
            Html::addCssClass($groupOptions, ['widget' => 'input-group']);
            $this->parts['{input}'] = Html::tag('div', $prepend . $this->parts['{input}'] . $append, $groupOptions);
        }

        return $this;
    }

    public function onoff($options = [])
    {
        Html::addCssClass($options, ['widget' => 'form-check-input']);
        Html::addCssClass($options['labelOptions'], ['widget' => 'form-check-label']);
        parent::checkbox($options);

        $this->parts['{input}'] = '<div class="form-check form-switch form-switch-onoff mb-3" dir="ltr">' .
            $this->parts['{input}'] . '</div>';

        return $this;
    }


    public function date($options = [], $useTime = true)
    {
        AirDatepicker3Asset::register(Yii::$app->view);

        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($this->model, $this->attribute);
        }
        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
        $js = "new AirDatepicker('#" . $options['id'] . "', {
        autoClose: false,
        timepicker: " . ($useTime ? 'true' : 'false') . ",
        selectedDates: ['" . ($useTime ? Yii::$app->formatter->asDate($value, 'php:Y-m-d H:i') : Yii::$app->formatter->asDate($value, 'php:Y-m-d')) . "'],
        dateFormat: 'dd.MM.yyyy',
        timeFormat: 'HH:mm',
        buttons: [
        {
            content(dp) {
                return 'Закрыть'
            },
            onClick(dp) {
                dp.hide()
            }
        },],
        onSelect({date, formattedDate, datepicker}) {
            $(datepicker.\$el).change();
        }, 
});";
        Yii::$app->view->registerJs($js);

        $options['data-range'] = 'false';
        $options['data-language'] = 'ru';
        if (!empty($value)) {
            $value = $useTime ? Yii::$app->formatter->asDate($value, 'php:d.m.Y H:i') : Yii::$app->formatter->asDate($value, 'php:d.m.Y');
        }
        $options['value'] = $value;

        return $this->extTextInput($options, ['append' => '<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>']);
//
//        $this->template = '<div class="input-group"><label for="' . $options['id'] . '" class="input-group-text bg-body text-body" title="Время начала"><i class="fa-solid fa-business-time"></i></label>{input}</div>';
//        return parent::textInput($options);
    }

//    public function date($options = [])
//    {
//        BootstrapDatepicker::register(Yii::$app->view);
//
//        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
//        if (!empty($value)) {
//            $value = Yii::$app->formatter->asDate($value, 'php:d.m.Y');
//        }
//        $options['value'] = $value;
//
//        $options['placeholder'] = 'dd.mm.yyyy';
//        $id = $this->getInputId() . '-dp';
//        $options['data-date-container'] = '#' . $id;
//        $options['data-date-format'] = 'dd.mm.yyyy';
//        $options['data-date-autoclose'] = 'true';
//        $options['data-provide'] = 'datepicker';
//        return $this->extTextInput($options, ['append' => '<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>', 'group-id' => $id]);
//    }


    /**
     * @var string|null optional template to render the `{input}` placeholder content
     */
    /*public $inputTemplate = '';
    public $inputExtTemplate = '{iconWrapperBegin}{input}{icon}{iconWrapperEnd}';

    public $inputIconClass = 'form-control-feedback';
    public $buttonsGroupClass = 'input-group-btn';

    public function textInput($options = [], $placeholder = false, $label = true, $icon = '', $buttons = '')
    {
        $this->processExtInput($placeholder, $label, $icon, $buttons);
        return parent::textInput($options);
    }

    public function dropDownList($items, $options = [], $label = true)
    {
        $this->label($label);
        return parent::dropDownList($items, $options);
    }

    public function textArea($options = [], $placeholder = false, $label = true, $icon = '')
    {
        $this->processExtInput($placeholder, $label, $icon);
        return parent::textArea($options);
    }

    public function passwordInput($options = [], $placeholder = false, $label = true, $icon = '')
    {
        $this->processExtInput($placeholder, $label, $icon);
        return parent::passwordInput($options);
    }

    protected function processExtInput($placeholder = false, $label = true, $icon = '', $buttons = '')
    {
        if($buttons) { // Process buttons
            $iconWrapperBegin = Html::beginTag('div', ['class' => 'input-group']);
            $iconWrapperEnd  = Html::endTag('div');
            $spanOptions = ['class' => ''];

            if($this->inputIconClass) Html::addCssClass($spanOptions, $this->buttonsGroupClass);
            $icon = Html::tag('span', (is_array($buttons) ? implode('', $buttons) : $buttons), $spanOptions);
        } else if($icon || $buttons) { // Process icon
            $iconWrapperBegin = Html::beginTag('div', ['style' => 'position: relative;']);
            $iconWrapperEnd  = Html::endTag('div');
            $spanOptions = ['class' => ''];

            if($this->inputIconClass) Html::addCssClass($spanOptions, $this->inputIconClass);
            Html::addCssClass($spanOptions, ['widget' => $icon]);
            $icon = Html::tag('span', '', $spanOptions);
        } else {
            $iconWrapperBegin = '';
            $iconWrapperEnd = '';
            $icon = '';
        }
        $this->inputTemplate = strtr($this->inputExtTemplate, ['{iconWrapperBegin}' => $iconWrapperBegin, '{icon}' => $icon, '{iconWrapperEnd}' => $iconWrapperEnd]);

        // Process placeholder
        if (is_bool($placeholder)) {
            if ($placeholder === true) {
                $attribute = Html::getAttributeName($this->attribute);
                $attrLabel = Html::encode($this->model->getAttributeLabel($attribute));
                $this->inputOptions['placeholder'] = $attrLabel;
            }
        } else {
            $this->inputOptions['placeholder'] = $placeholder;
        }

        // Process label
        $this->label($label);
    }*/


    /**
     * MediaUpload $options example
     *
     * $options[
     *      'options'=>[
     *          'accept' => 'image/jpeg' // 'accept' => 'image/jpeg,image/png' // 'accept' => 'image/*'
     *      ],
     *      'pluginOptions' => [
     *          'allowedFileExtensions' => ['avi', 'mpeg'],
     *          'allowedFileTypes' => ["image", "video"],
     *          'uploadUrl' => Url::to(['/site/file-upload']),
     *          'uploadExtraData' => [
     *              'album_id' => 20,
     *              'cat_id' => 'Nature'
     *          ],
     *          'maxFileCount' => 10,
     *          'initialPreview'=>[
     *              Html::img("/images/moon.jpg", ['class'=>'file-preview-image', 'alt'=>'The Moon', 'title'=>'The Moon']),
     *          ],
     *          'initialCaption'=>"The Moon and the Earth",
     *          'overwriteInitial'=>false,
     *          'showPreview' => false,
     *          'showCaption' => true,
     *          'showRemove' => true,
     *          'showUpload' => false,
     *          'browseLabel' => 'Browse',
     *          'browseClass' => 'btn btn-success',
     *          'browseIcon' => ''
     *          'removeLabel' => '',
     *          'removeClass' => 'btn btn-danger',
     *          'removeIcon' => '<i class="glyphicon glyphicon-trash"></i> '
     *          'uploadLabel' => '',
     *          'uploadClass' => 'btn btn-info',
     *          'uploadIcon' => '',
     *          'mainClass' => 'input-group-lg',
     *          'previewFileType' => 'image', // 'any'
     *          'elCaptionText' => '#customCaption'
     *          'elErrorContainer' => "#errorBlock",
     *      ]
     */

    /**
     * @param array $options
     * @param bool $multipart
     * @return string
     * @throws \Exception
     */
    public function mediaUpload($options = [], $multipart = true)
    {
        $multiple     = false;
        $fileExts     = [];
        $ajaxUrl      = true;
        $mediaUpload  = Yii::$app->upload;
        $fileTempId   = $mediaUpload->getTempId();

        if ($multipart) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }

        if(!empty($options['pluginOptions']['uploadUrl'])) $pageUrl = $options['pluginOptions']['uploadUrl'];
        else $pageUrl = $mediaUpload->getUrl();

        if (strpos($this->attribute, '[')) {
            $simpleAttribute = explode('[', $this->attribute);
            $simpleAttribute = array_shift($simpleAttribute);
        } else {
            $simpleAttribute = $this->attribute;
        }

        // Get params from behavior
        if($this->model)
        {
            foreach($this->model->getBehaviors() as $behavior)
            {
                if($behavior instanceof MediaBehavior && $behavior->media[$simpleAttribute]) {
                    $mediaBehaviorParams = $behavior->media[$simpleAttribute];
                    if(isset($mediaBehaviorParams['multiple']))  $multiple  = $mediaBehaviorParams['multiple'];
                    if(isset($mediaBehaviorParams['files']))     $fileExts  = $mediaBehaviorParams['files'];
                    if(isset($mediaBehaviorParams['ajax']))      $ajaxUrl   = $mediaBehaviorParams['ajax'];

                    $images = MediaFile::getFiles($this->attribute, $this->model->primaryKey, $fileTempId);

                    $initialFiles = [];
                    $initialFilesConfig = [];
                    $previewSize = isset($mediaBehaviorParams['previewSize']) ? (int)$mediaBehaviorParams['previewSize'] : $mediaUpload->previewSize;
                    foreach($images as $image) {
                        $initialFiles[] = $mediaUpload->getMediaPreview($previewSize, $image);
                        $initialFilesConfig[] = $mediaUpload->getMediaPreviewConfig($previewSize, $image, $this->model, $this->attribute, $pageUrl);
                    }
                    $options['pluginOptions']['initialPreview'] = $initialFiles;
                    $options['pluginOptions']['initialPreviewConfig'] = $initialFilesConfig;
                    $options['pluginOptions']['overwriteInitial'] = false;
                    break;
                }
            }
        }

        $attribute = $this->attribute;
        if($multiple) $attribute .= '[]';
        $ajaxAttr  = $this->attribute;

        $options['model'] = $this->model;
        $options['attribute'] = $attribute;
        //$options['name'] = $attribute;
        if(is_bool($multiple)) {
            $options['options']['multiple'] = $multiple ? true: false;
        } else {
            $multiple = (int)$multiple;
            $options['options']['multiple'] = $multiple > 1 ? true : false;
            $options['pluginOptions']['maxFileCount'] = $multiple > 1 ? $multiple : 1;
        }
        $options['options']['multiple'] = $multiple ? true: false;


        if(!isset($options['pluginOptions']['removeClass'])) $options['pluginOptions']['removeClass'] = 'btn btn-danger';
        if(!isset($options['pluginOptions']['uploadClass'])) $options['pluginOptions']['uploadClass'] = 'btn btn-primary';
        if(!isset($options['pluginOptions']['browseClass'])) $options['pluginOptions']['browseClass'] = 'btn btn-default';

        $modelKey = $this->model->getPrimaryKey();
        $modelClass = get_class($this->model);

        $options['pluginOptions']['theme'] = 'bs5';
        if(!$ajaxUrl) {
            $options['pluginOptions']['showRemove'] = false;
            $options['pluginOptions']['showUpload'] = false;
            $options['pluginOptions']['removeLabel'] = Yii::t('admin', 'Clear');
        } else {
            $options['pluginOptions']['showRemove'] = false;
            $options['pluginOptions']['showUpload'] = true;
            $options['pluginOptions']['uploadUrl'] = $pageUrl;
            $options['pluginOptions']['uploadExtraData'] = [
                'modelClass' => $modelClass,
                'ajaxUpload' => 1,
                'fileTempId' => $fileTempId,
                'modelKey'   => $modelKey,
                'category'   => $ajaxAttr,
            ];
        }

        $accept = [];
        foreach((array)$fileExts as $fileType) {
            $mime = BaseFileHelper::getMimeTypeByExtension('test.' . $fileType);
            if($mime) {
                $accept[$mime] = $mime;
            }
        }
        $options['options']['accept'] = !empty($options['options']['accept']) ? $options['options']['accept'] . ',' . implode(',', $accept) : implode(',', $accept);
        $options['pluginOptions']['allowedFileExtensions'] = isset($options['pluginOptions']['allowedFileExtensions']) ? array_merge((array)$options['pluginOptions']['allowedFileExtensions'], $fileExts) : $fileExts;

        //if(!isset($options['language'])) $options['language'] = \system\helpers\Language::getLanguageIso2(\Yii::$app->language);

        static $i = 0;
        $inputId = Html::getInputId($this->model, $attribute) . '-' . ++$i;

        $options['options']['id'] = $inputId;
        $attr = explode('[', $attribute);

        $label = $options['label'] ?? $this->model->getAttributeLabel($simpleAttribute);
        unset($options['label']);

        $html = '<div class="form-group"><label class="control-label">' . $label . '</label>' .
            Html::hiddenInput('fileTempId', $fileTempId) . \kartik\file\FileInput::widget($options) . '</div>';

        $modelClassX = str_replace('\\', '\\\\', $modelClass);
        $js = <<<JS
jQuery('#$inputId').on('filesorted', function(e, params) {
    var order = {};
    for(var i in params.stack) order[i] = params.stack[i].key;

    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "$pageUrl",
        data: {
            modelClass:  '$modelClassX',
            ajaxReorder: 1,
            order:       order,
            modelKey:    '$modelKey',
            category:    '$ajaxAttr'
        }
    });
});
JS;
        Yii::$app->view->registerJs($js);

        return $html;
    }
    /**
     * @param array $options
     * @param bool $multipart
     * @return string
     * @throws \Exception
     */
    public function fieldUpload($options = [], $multipart = true)
    {
        $multiple     = false;
        $fileExts     = [];
        $mediaUpload  = Yii::$app->upload;
        $fileTempId   = $mediaUpload->getTempId();

        if ($multipart) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }

        if(!empty($options['pluginOptions']['uploadUrl'])) $pageUrl = $options['pluginOptions']['uploadUrl'];
        else $pageUrl = $mediaUpload->getUrl();

        $images = $this->model->{$this->attribute};
        if (!is_array($images)) {
            if ($images) $images = [$images];
            else $images = [];
        }
        $initialFiles = [];
        $initialFilesConfig = [];
        $previewSize = isset($options['previewSize']) ? (int)$options['previewSize'] : $mediaUpload->previewSize;
        foreach($images as $image) {
            $initialFiles[] = $mediaUpload->getMediaPreviewSimple($previewSize, $image);
            $initialFilesConfig[] = $mediaUpload->getMediaPreviewConfigSimple($previewSize, $image, $this->model, $this->attribute, );
        }
        $options['pluginOptions']['initialPreview'] = $initialFiles;
        $options['pluginOptions']['initialPreviewConfig'] = $initialFilesConfig;
        $options['pluginOptions']['overwriteInitial'] = false;

        $attribute = $this->attribute;
        if($multiple) $attribute .= '[]';
        $ajaxAttr  = $this->attribute;

        $options['model'] = $this->model;
        $options['attribute'] = $attribute;
        $options['options']['multiple'] = false;
        $options['options']['hiddenOptions']['name'] = '';
        $options['pluginOptions']['maxFileCount'] = 1;

        if(!isset($options['pluginOptions']['removeClass'])) $options['pluginOptions']['removeClass'] = 'btn btn-danger';
        if(!isset($options['pluginOptions']['uploadClass'])) $options['pluginOptions']['uploadClass'] = 'btn btn-primary';
        if(!isset($options['pluginOptions']['browseClass'])) $options['pluginOptions']['browseClass'] = 'btn btn-default';

        $modelKey = $this->model->getPrimaryKey();
        $modelClass = get_class($this->model);

        $options['pluginOptions']['theme'] = 'bs5';
        $options['pluginOptions']['showRemove'] = false;
        $options['pluginOptions']['showUpload'] = false;
        $options['pluginOptions']['showCancel'] = false;
        $options['pluginOptions']['removeLabel'] = Yii::t('admin', 'Clear');

        $accept = [];
        foreach((array)$fileExts as $fileType) {
            $mime = BaseFileHelper::getMimeTypeByExtension('test.' . $fileType);
            if($mime) {
                $accept[$mime] = $mime;
            }
        }
        $options['options']['accept'] = !empty($options['options']['accept']) ? $options['options']['accept'] . ',' . implode(',', $accept) : implode(',', $accept);
        $options['pluginOptions']['allowedFileExtensions'] = isset($options['pluginOptions']['allowedFileExtensions']) ? array_merge((array)$options['pluginOptions']['allowedFileExtensions'], $fileExts) : $fileExts;

        //if(!isset($options['language'])) $options['language'] = \system\helpers\Language::getLanguageIso2(\Yii::$app->language);

        static $i = 0;
        $inputId = Html::getInputId($this->model, $attribute) . '-' . ++$i;

        $options['options']['id'] = $inputId;
        $attr = explode('[', $attribute);

        $label = $options['label'] ?? $this->model->getAttributeLabel($simpleAttribute);
        unset($options['label']);

        $html = '<div class="form-group"><label class="control-label">' . $label . '</label>' .
            \kartik\file\FileInput::widget($options) . '</div>';

        $modelClassX = str_replace('\\', '\\\\', $modelClass);
        $js = <<<JS
jQuery('#$inputId').on('filesorted', function(e, params) {
    var order = {};
    for(var i in params.stack) order[i] = params.stack[i].key;

    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "$pageUrl",
        data: {
            modelClass:  '$modelClassX',
            ajaxReorder: 1,
            order:       order,
            modelKey:    '$modelKey',
            category:    '$ajaxAttr'
        }
    });
});
JS;
        Yii::$app->view->registerJs($js);

        return $html;
    }



    protected $noWrap = false;

    /**
     * Renders the opening tag of the field container.
     * @return string the rendering result.
     */
    public function begin()
    {
        if ($this->noWrap) {
            if ($this->form->enableClientScript) {
                $clientOptions = $this->getClientOptions();
                if (!empty($clientOptions)) {
                    $this->form->attributes[] = $clientOptions;
                }
            }

            return '';
        }

        return parent::begin();
    }

    protected function setupNoWrap(&$options)
    {
        $this->noWrap = true;

        $inputID = $this->getInputId();
        $attribute = Html::getAttributeName($this->attribute);

        $class = isset($options['class']) ? (array) $options['class'] : [];
        $class[] = "field-$inputID";
        if ($this->model->isAttributeRequired($attribute)) {
            $class[] = $this->form->requiredCssClass;
        }
        Html::addCssClass($class, 'form-control');
        $options['class'] = implode(' ', $class);
        if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_CONTAINER) {
            $this->addErrorClassIfNeeded($options);
        }
    }

    /**
     * Renders the closing tag of the field container.
     * @return string the rendering result.
     */
    public function end()
    {
        if ($this->noWrap) {
            return '';
        }
        return parent::end();
    }
}
