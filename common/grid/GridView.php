<?php

namespace common\grid;

use common\widgets\LinkPager;
use yii\grid\Column;
use yii\helpers\Html;

class GridView extends \yii\grid\GridView
{
    //public $tableOptions = ['class' => 'table table-sm table-striped table-bordered mb-0'];
    public $tableOptions = ['class' => 'table align-middle table-check table-striped mb-0'];

    public $responsive = true;

    public $headerRowOptions = ['class' => 'table-light'];

    public $layout = '
<div class="row py-2 d-flex justify-content-between align-items-center"><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-start">{actions}</div><div class="col-md col-sm-12 d-flex justify-content-center">{pager}</div><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-end">{summary}</div></div>
{items}
<div class="row py-2 d-flex justify-content-between align-items-center"><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-start">{actions}</div><div class="col-md col-sm-12 d-flex justify-content-center">{pager}</div><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-end">{summary}</div></div>
';

    public $actions = '';

    public $pager = ['class' => 'common\widgets\LinkPager'];

    /**
     * {@inheritdoc}
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{actions}':
                return $this->actions;
            case '{items}':
                $items = parent::renderSection($name);
                if ($this->responsive) {
                    $items = '<div class="table-responsive">' . $items . '</div>';
                }
                return $items;
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "<thead>\n" . $content . "\n" . ($this->showHeaderFooter ? '<!-- theadfoot -->' : '') . "</thead>";
    }

    public $showHeaderFooter = false;
    protected $_theadfoot = '';

    /**
     * Renders the table footer.
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        $this->_theadfoot = $content;

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the data models for the grid view.
     * @return string the HTML code of table
     */
    public function renderItems()
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();

        $tableFooter = false;
        $tableFooterAfterBody = false;

        if ($this->showFooter) {
            if ($this->placeFooterAfterBody) {
                $tableFooterAfterBody = $this->renderTableFooter();
            } else {
                $tableFooter = $this->renderTableFooter();
            }
        }
        if ($this->showHeaderFooter) {
            $tableHeader = str_replace('<!-- theadfoot -->', $this->_theadfoot, $tableHeader);
        }

        $content = array_filter([
            $caption,
            $columnGroup,
            $tableHeader,
            $tableFooter,
            $tableBody,
            $tableFooterAfterBody,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }
}
