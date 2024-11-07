<?php

namespace common\theme\skote\widgets;

use common\helpers\Access;
use Yii;
use yii\helpers\Url;

class Menu extends \yii\base\Widget
{
    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if (!(Yii::$app->urlManager instanceof \common\web\UrlManager)) {
            die('Menu must be used with common\web\UrlManager');
        }
    }

    /**
     * Renders submenu tree
     * @param array $menuItems - array of submenu items
     * @param array $groups - array [groupId => groupTitle]
     * @return string - Submenu HTML
     */
    protected function renderSubmenu($menuItems, $submenuId)
    {
        if (!is_array($menuItems) || count($menuItems) == 0) return '';
        $items = [];

        $lastGroup = '';
        foreach($menuItems as $item) {
            if (empty($item['label'])) continue;

            $subItems = isset($item['items']) && is_array($item['items']) ? $item['items'] : [];
            $group = $item['group'] ?? '';
            if (count($subItems) > 0) {
//                $id = 'menu' . (++static::$idx);
//                $subMenu = $this->renderSubmenu($subItems, $id);
//                if ($subMenu) {
//
//                    if ($group != $lastGroup) {
//                        if (count($items) > 0) $items[] = '<div class="dropdown-divider"></div>';
//                        $lastGroup = $group;
//                    }
//
//                    $items[] = '<div class="dropdown"><a href="#" id="' . $id . '" class="dropdown-item dropdown-toggle arrow-none' . ($item['active'] ?? false ? ' active' : '') . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $item['label'] . '<div class="arrow-down"></div></a>
//						' . $subMenu . '</div>';
//                }
            } else {
                if (!isset($item['url'])) $item['url'] = ['/'];
                else if (!is_array($item['url'])) $item['url'] = [$item['url']];

                if (Access::checkAccess($item['access'] ?? '$', $item['url'])) {

                    if ($group != $lastGroup) {
                        if (count($items) > 0) $items[] = '<div class="dropdown-divider"></div>';
                        $lastGroup = $group;
                    }
                    $label = (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<a href="' . Url::to($item['url']) . '" class="dropdown-item' . ($item['active'] ?? false ? ' active' : '') . '">' . $label . '</a>';
                }
            }
        }
        if (count($items) == 0) return '';


        $result = '<div class="dropdown-menu" aria-labelledby="' . $submenuId . '">';
        $result .= implode('', $items);
        $result .= '</div>';
        return $result;
    }

    public static $idx = 0;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $items = [];
        foreach(Yii::$app->urlManager->menuData as $item) {
            if (empty($item['label'])) continue;

            $subItems = isset($item['items']) && is_array($item['items']) ? $item['items'] : [];
            if (count($subItems) > 0) {
                $id = 'menu' . (++static::$idx);
                $subMenu = $this->renderSubmenu($subItems, $id);
                if ($subMenu) {
                    $label = (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<li class="dropdown nav-item">
                        <a href="#" id="' . $id . '" class="dropdown-toggle arrow-none nav-link' . ($item['active'] ?? false ? ' active' : '') . '" data-bs-toggle="dropdown" aria-expanded="false"><span>' . $label . '</span><div class="arrow-down"></div></a>
                        ' . $subMenu . '</li>';
                }
            } else {
                if (!isset($item['url'])) $item['url'] = ['/'];
                else if (!is_array($item['url'])) $item['url'] = [$item['url']];

                if (Access::checkAccess($item['access'] ?? '$', $item['url'])) {
                    $label = (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<li class="nav-item">' .
                        '<a href="' . Url::to($item['url']) . '" class="nav-link' . ($item['active'] ?? false ? ' active' : '') . '">' . $label . '</a></li>';
                }
            }
        }
        $result = '<nav class="navbar navbar-light navbar-expand-lg topnav-menu">';
        $result .= '<div class="collapse navbar-collapse" id="topnav-menu-content">';
        $result .= '<ul class="navbar-nav" id="main-menu-navigation">';
        $result .= implode('', $items);
        $result .= '</ul>';
        $result .= '</div>';
        $result .= '</nav>';
        return $result;
    }
}
