<?php

namespace common\grid;

class NumericColumn extends \yii\grid\DataColumn
{
    public $format = 'raw';
    public $thisPage = true;
    public $total = 0;
    public $cnt = 0;
    public $middleTotal = false;
    public $customTotal = false;
    public $decimal = 2;
    public $emptyValue = '';
    public $clearZeroes = true;
    public $decimalSep = '.';
    public $thousandSep = ' ';
    public $unit = '';
    public $totals = [];
    public static $staticTotals = [];

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        if ($value) {

            if($this->decimal == 0) $value = (int)$value;
            else $value = (float)$value;

            if($this->total !== FALSE) {
                $this->total += $value;
                $this->cnt++;
            }

            if ($this->content != null) {
                return call_user_func($this->content, $model, $key, $index, $this);
            }

            $value = number_format($value, $this->decimal, $this->decimalSep, $this->thousandSep);
            if ($this->decimal > 0 && $this->clearZeroes) {
                $value = rtrim(rtrim($value, '0'), $this->decimalSep);
            }

            if($this->unit) {
                $value .= ' ' . $this->unit;
            }
            return $value;
        }

        if ($this->emptyValue && $this->decimal == 0) {
            return $this->emptyValue;
        }
        return parent::renderDataCellContent($model, $key, $index);
    }

    /**
     * Renders the footer cell content.
     * The default implementation simply renders [[footer]].
     * This method may be overridden to customize the rendering of the footer cell.
     * @return string the rendering result
     */
    protected function renderFooterCellContent()
    {
        if ($this->total === FALSE) return '&nbsp;';
        
        if ($this->customTotal instanceof \Closure) {
            $tmp = $this->customTotal;
            $this->total = $tmp();
        }

        $html = [];
        if ($this->thisPage) {

            if ($this->middleTotal) {
                $total = number_format($this->cnt > 0 ? $this->total / $this->cnt : 0, $this->decimal, $this->decimalSep, $this->thousandSep);
                static::$staticTotals[$this->attribute] = number_format($this->cnt > 0 ? $this->total / $this->cnt : 0, $this->decimal, '.', '');
            } else {
                $total = number_format($this->total, $this->decimal, $this->decimalSep, $this->thousandSep);
                static::$staticTotals[$this->attribute] = number_format($this->total, $this->decimal, '.', '');
            }



            if ($this->decimal > 0 && $this->clearZeroes) {
                $total = rtrim(rtrim($total, '0'), $this->decimalSep);
            }

            $value = '<span class="text-nowrap" title="' . 'На этой странице' . '">' . $total;
            if ($this->unit) {
                $value .= ' ' . $this->unit;
            }
            $value .= '</span>';
            $html[] = $value;
        }

        if (!$this->customTotal && !$this->middleTotal && is_array($this->totals) && isset($this->totals[$this->attribute])) {

            $total = number_format($this->totals[$this->attribute], $this->decimal, $this->decimalSep, $this->thousandSep);
            static::$staticTotals[$this->attribute] = number_format($this->totals[$this->attribute], $this->decimal, '.', '');
            if ($this->decimal > 0 && $this->clearZeroes) {
                $total = rtrim(rtrim($total, '0'), $this->decimalSep);
            }

            $value = '<span class="text-nowrap" title="' . 'На всех страницах' . '">' . $total;
            if($this->unit) {
                $value .= ' ' . $this->unit;
            }
            $value .= '</span>';
            $html[] = $value;
        }

        return implode(' / ', $html);
    }
}
