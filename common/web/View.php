<?php

namespace common\web;

class View extends \yii\web\View
{
    public $replaceAssetBundles = [];

    /**
     * @inheritDoc
     */
    public function registerAssetBundle($name, $position = null)
    {
//        if (isset($this->assetBundles[$name])) {
//            if ($this->assetBundles[$name] === false) {
//                // Prevent circular loading
//                return;
//            } else {
//                // Return already loaded
//                return $this->assetBundles[$name];
//            }
//        }

        if (!empty($this->replaceAssetBundles[$name])) {
            // Replace asset bundle
            $bundle = parent::registerAssetBundle($this->replaceAssetBundles[$name], $position);
            $this->assetBundles[$name] = $bundle;
            return $bundle;
        }

        return parent::registerAssetBundle($name, $position);
    }
}
