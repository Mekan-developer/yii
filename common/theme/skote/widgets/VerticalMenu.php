<?php

namespace common\theme\skote\widgets;

use common\helpers\Access;
use Yii;
use yii\helpers\Url;

class VerticalMenu extends \yii\base\Widget
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
     * @return string - Submenu HTML
     */
    protected function renderSubmenu($menuItems)
    {
        if (!is_array($menuItems) || count($menuItems) == 0) return '';
        $items = [];

        foreach($menuItems as $item) {
            if (empty($item['label'])) continue;

            $subItems = isset($item['items']) && is_array($item['items']) ? $item['items'] : [];
            if (count($subItems) > 0) {
            } else {
                if (!isset($item['url'])) $item['url'] = ['/'];
                else if (!is_array($item['url'])) $item['url'] = [$item['url']];

                if (Access::checkAccess($item['access'] ?? '$', $item['url'])) {
                    $items[] = '<li' . ($item['active'] ?? false ? ' class="mm-active"' : '') . '><a href="' . Url::to($item['url']) . '">' .
                        (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') .
                        $item['label'] . '</a></li>';
                }
            }
        }
        if (count($items) == 0) return '';

        $result = '<ul class="sub-menu" aria-expanded="false">';
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
            if (!is_array($item)) {
                $items[] = '<li class="menu-title" key="t-menu">' . (string)$item . '</li>';
                continue;
            }
            if (empty($item['label'])) continue;

            $subItems = isset($item['items']) && is_array($item['items']) ? $item['items'] : [];
            if (count($subItems) > 0) {
                $subMenu = $this->renderSubmenu($subItems);
                if ($subMenu) {
                    $items[] = '<li' . ($item['active'] ?? false ? ' class="mm-active"' : '') . '>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            ' . (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') . '
                            <span>' . $item['label'] . '</span>
                        </a>
                        ' . $subMenu . '</li>';
                }
            } else {
                if (!isset($item['url'])) $item['url'] = ['/'];
                else if (!is_array($item['url'])) $item['url'] = [$item['url']];

                if (Access::checkAccess($item['access'] ?? '$', $item['url'])) {
                    $items[] = '<li' . ($item['active'] ?? false ? ' class="mm-active"' : '') . '>
                        <a href="' . Url::to($item['url']) . '" class="waves-effect">
                            ' . (($item['icon'] ?? '') ? '<i class="menu-icon ' . $item['icon'] . '"></i>' : '') . '
                            <span>' . $item['label'] . '</span>
                        </a>
                    </li>';
                }
            }
        }
        $result = '<div id="sidebar-menu">';
        $result .= '<ul class="metismenu list-unstyled" id="side-menu">';
        $result .= implode('', $items);
        $result .= '</ul>';
        $result .= '</div>';
        return $result;
    }
}
