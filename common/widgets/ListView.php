<?php

namespace common\widgets;

class ListView extends \yii\widgets\ListView
{

    public $layout = '
<div class="row py-2 d-flex justify-content-between align-items-center"><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-start">{actions}</div><div class="col-md col-sm-12 d-flex justify-content-center">{pager}</div><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-end">{summary}</div></div>
<div class="row">{items}</div>
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
            default:
                return parent::renderSection($name);
        }
    }
}
