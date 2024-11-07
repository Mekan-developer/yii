<?php

namespace common\web;

use yii\helpers\Html;
use Yii;

class UrlManager extends \yii\web\UrlManager {

    public $menuData = [];
    public $skipAppId = false;
    protected $titleItems = [];
    protected $breadItems = [];
    protected $sidebarMenu = null;

    public function getFullTitle()
    {
        return implode(' / ', $this->titleItems);
    }

    public function clear()
    {
        $this->titleItems = [];
        $this->breadItems = [];
    }

    public function getLastTitle()
    {
        return $this->titleItems[0] ?? '';
    }

    public function getSidebarMenu()
    {
        return $this->sidebarMenu;
    }

    public function getSidebar()
    {
        return is_array($this->sidebarMenu);
    }

    public function getBreadcrumbs()
    {
        return $this->breadItems;
    }

    public function addTitle($title)
    {
        array_unshift($this->titleItems, $title);
    }

    public function addBreadcrumb($item)
    {
        $this->breadItems[] = $item;
    }

    /**
     * Initializes UrlManager.
     */
    public function init()
    {
        parent::init();

        $currentPath =  '/' . trim(Yii::$app->request->pathInfo, '/');
        if ($currentPath) {
            $this->findCurrent($this->menuData, $currentPath, 0);
        }
    }

    protected function findCurrent(&$items, $currentPath, $level)
    {
        foreach($items as $k => $item) {
            if (!is_array($item)) continue;
            if (isset($item['items']) && is_array($item['items'])) {
                $updatedItems = $this->findCurrent($items[$k]['items'], $currentPath, $level+1);
                if ($updatedItems) {
                    $items[$k]['active'] = true;
                    array_push($this->titleItems, strip_tags(str_replace('&nbsp;', '', $item['label'])));
                    array_unshift($this->breadItems, $item['label']);

                    if ($level == 0) {
                        $this->sidebarMenu = $items[$k];
                    }
                    return $item;
                }
            } else {
                $url = '/' . trim($item['url'] ?? '', '/');
                if ($url == '/') $url = '/site';
                if ($currentPath == '/') $currentPath = '/site';
                if ($url && strpos($currentPath . '/', $url . '/') === 0) {
                    $items[$k]['active'] = true;
                    array_push($this->titleItems, strip_tags(str_replace('&nbsp;', '', $item['label'])));
                    array_unshift($this->breadItems, ['label' => $item['label'], 'url' => $url]);
                    return $item;
                }
            }
        }
    }

    public function getTitle($title, $glue = ' / ')
    {
        if ($title) array_unshift($this->titleItems, $title);
        $skipAppId = (property_exists(Yii::$app->urlManager, 'skipAppId') && Yii::$app->urlManager->skipAppId);
        if ($skipAppId) array_push($this->titleItems, Yii::$app->name);
        return Html::encode(implode($glue, $this->titleItems));
    }
}
