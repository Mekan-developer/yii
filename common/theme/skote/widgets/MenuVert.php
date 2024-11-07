<?php

namespace common\theme\skote\widgets;

use Yii;
use yii\helpers\Url;
use common\helpers\Access;

class MenuVert extends \yii\base\Widget
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
                        if (count($items) > 0) $items[] = '<li class="menu-divider"></li>';
                        $lastGroup = $group;
                    }
                    $label = (!empty($item['icon']) ? '<i class="' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<li><a href="' . Url::to($item['url']) . '" class="' . ($item['active'] ?? false ? ' active' : '') . '">' . $label . '</a></li>';
                }
            }
        }
        if (count($items) == 0) return '';


        $result = '<ul class="sub-menu">';
        $result .= implode('', $items);
        $result .= '</ul>';
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
                    $label = ($item['icon'] ? '<i class="' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<li>
                        <a href="#" id="' . $id . '" class="waves-effect' . ($item['active'] ?? false ? ' active' : '') . '" data-bs-toggle="dropdown" aria-expanded="false">' . $label . '<span><div class="arrow-down"></div></span></a>
                        ' . $subMenu . '</li>';
                }
            } else {
                if (!isset($item['url'])) $item['url'] = ['/'];
                else if (!is_array($item['url'])) $item['url'] = [$item['url']];

                if (Access::checkAccess($item['access'] ?? '$', $item['url'])) {
                    $label = (!empty($item['icon']) ? '<i class="' . $item['icon'] . '"></i>' : '') . '<span>' . $item['label'] . '</span>';
                    $items[] = '<li class="' . ($item['active'] ?? false ? 'mm-active' : '') . '">' .
                        '<a href="' . Url::to($item['url']) . '" class="waves-effect ' . ($item['active'] ?? false ? ' active' : '') . '">' . $label . '</a></li>';
                }
            }
        }

        $result = '<ul class="metismenu list-unstyled" id="side-menu">';
        $result .= implode('', $items);
        $result .= '</ul>';
        return $result;
    }
}
