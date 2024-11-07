<?php

namespace common\theme\skote\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Breadcrumbs displays a list of links indicating the position of the current page in the whole site hierarchy.
 *
 * For example, breadcrumbs like "Home / Sample Post / Edit" means the user is viewing an edit page
 * for the "Sample Post". He can click on "Sample Post" to view that page, or he can click on "Home"
 * to return to the homepage.
 *
 * To use Breadcrumbs, you need to configure its [[links]] property, which specifies the links to be displayed. For example,
 *
 * ```php
 * // $this is the view object currently being used
 * echo Breadcrumbs::widget([
 *     'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
 *     'links' => [
 *         [
 *             'label' => 'Post Category',
 *             'url' => ['post-category/view', 'id' => 10],
 *             'template' => "<li><b>{link}</b></li>\n", // template for this link only
 *         ],
 *         ['label' => 'Sample Post', 'url' => ['post/edit', 'id' => 1]],
 *         'Edit',
 *     ],
 * ]);
 * ```
 *
 * Because breadcrumbs usually appears in nearly every page of a website, you may consider placing it in a layout view.
 * You can use a view parameter (e.g. `$this->params['breadcrumbs']`) to configure the links in different
 * views. In the layout view, you assign this view parameter to the [[links]] property like the following:
 *
 * ```php
 * // $this is the view object currently being used
 * echo Breadcrumbs::widget([
 *     'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
 * ]);
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Breadcrumbs extends Widget
{
    /**
     * @var string the name of the breadcrumb container tag.
     */
    public $tag = 'div';
    /**
     * @var array the HTML attributes for the breadcrumb container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'row breadcrumbs-top mb-3'];
    /**
     * @var bool whether to HTML-encode the link labels.
     */
    public $encodeLabels = false;
    /**
     * @var array the first hyperlink in the breadcrumbs (called home link).
     * Please refer to [[links]] on the format of the link.
     * If this property is not set, it will default to a link pointing to [[\yii\web\Application::homeUrl]]
     * with the label 'Home'. If this property is false, the home link will not be rendered.
     */
    public $homeLink;// = ['url' => ['/'], 'label' => Yii::t('admin', 'Home')];
    /**
     * @var array list of links to appear in the breadcrumbs. If this property is empty,
     * the widget will not render anything. Each array element represents a single link in the breadcrumbs
     * with the following structure:
     *
     * ```php
     * [
     *     'label' => 'label of the link',  // required
     *     'url' => 'url of the link',      // optional, will be processed by Url::to()
     *     'template' => 'own template of the item', // optional, if not set $this->itemTemplate will be used
     * ]
     * ```
     *
     * If a link is active, you only need to specify its "label", and instead of writing `['label' => $label]`,
     * you may simply use `$label`.
     *
     * Since version 2.0.1, any additional array elements for each link will be treated as the HTML attributes
     * for the hyperlink tag. For example, the following link specification will generate a hyperlink
     * with CSS class `external`:
     *
     * ```php
     * [
     *     'label' => 'demo',
     *     'url' => 'http://example.com',
     *     'class' => 'external',
     * ]
     * ```
     *
     * Since version 2.0.3 each individual link can override global [[encodeLabels]] param like the following:
     *
     * ```php
     * [
     *     'label' => '<strong>Hello!</strong>',
     *     'encode' => false,
     * ]
     * ```
     */
    public $links = [];

    /**
     * Renders the widget.
     */
    public function run()
    {
        /*if (empty($this->links)) {
            return;
        }*/
        $links = [];
        if ($this->homeLink === null) {
            $links[] = $this->renderItem([
                'label' => Yii::t('yii', 'Home'),
                'url' => Yii::$app->homeUrl,
            ]);
        } elseif ($this->homeLink !== false) {
            $links[] = $this->renderItem($this->homeLink);
        }

        $sourceLinks = $this->links ?? [];
        if (Yii::$app->urlManager instanceof \common\web\UrlManager) {
            $sourceLinks = Yii::$app->urlManager->breadcrumbs ?? [];
        }

        if (is_array($sourceLinks)) {
            foreach ($sourceLinks as $link) {
                if (!is_array($link)) {
                    $link = ['label' => $link];
                }
                $links[] = $this->renderItem($link);
            }
        }

        $html = '<ol class="breadcrumb m-0">' . implode('', $links) . '</ol>';
        echo $html;
    }

    /**
     * Renders a single breadcrumb item.
     * @param array $link the link to be rendered. It must contain the "label" element. The "url" element is optional.
     * @param string $template the template to be used to rendered the link. The token "{link}" will be replaced by the link.
     * @return string the rendering result
     * @throws InvalidConfigException if `$link` does not have "label" element.
     */
    protected function renderItem($link)
    {
        $encodeLabel = ArrayHelper::remove($link, 'encode', $this->encodeLabels);
        if (array_key_exists('label', $link)) {
            $label = $encodeLabel ? Html::encode($link['label']) : $link['label'];
        } else {
            throw new InvalidConfigException('The "label" element is required for each link.');
        }

        if (isset($link['url'])) {
            $label = Html::a($label, Url::to(is_array($link['url']) ? $link['url'] : [$link['url']]));
        }

        return '<li class="breadcrumb-item' . (!empty($link['url']) ? '' : ' acive') . '">' . $label . '</li>';
    }

    static public $headerHtml = '';
}
