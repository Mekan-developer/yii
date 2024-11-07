<?php

namespace backend\controllers;

use common\controllers\BaseController;
use common\models\Order;
use common\models\Region;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Orders Controller
 */
class OrdersController extends BaseController
{
    public $modelClass = Order::class;

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';

    protected function newModel()
    {
        $model = parent::newModel();
        $model->prepareNewModel();
        return $model;
    }

    public function _getHeaderStyle() {
        return array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE,
            ),
            'font' => array('bold' => true),
            'fill' => array(
                'fillType'       => Fill::FILL_SOLID,
                'startColor' => array(
                    'argb' => 'FFF4CCCC',
                )
            )
        );
    }

    public function _getRowStyle() {
        return array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE,
            ),
            'numberFormat' => array('code' => NumberFormat::FORMAT_NUMBER)
        );
    }

    protected function getSheet($xls, $idx = 0, $title = 'Данные')
    {
        static $sheets = [];

        if (!isset($sheets[$idx])) {
            if ($idx == 0) {
                $sheets[$idx] = $xls->getActiveSheet();
                $sheets[$idx]->setTitle($title);
            } else {
                $sheets[$idx] = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($xls, $title);
                $xls->addSheet($sheets[$idx], $idx + 1);
            }

            $dataSheet = $sheets[$idx];

            $dataSheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $dataSheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $margins = $dataSheet->getPageMargins();
            $margins->setLeft(0.1);
            $margins->setRight(0.1);
            $margins->setTop(0.1);
            $margins->setBottom(0.1);

            $head_style = $this->_getHeaderStyle();

            $dataSheet->getColumnDimension('A')->setAutoSize(true);
            $dataSheet->setCellValue('A1', 'ID заказа');
            $dataSheet->getStyle('A1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('B')->setAutoSize(true);
            $dataSheet->setCellValue('B1', 'Дата');
            $dataSheet->getStyle('B1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('C')->setAutoSize(true);
            $dataSheet->setCellValue('C1', 'ФИО');
            $dataSheet->getStyle('C1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('D')->setAutoSize(true);
            $dataSheet->setCellValue('D1', 'Телефон');
            $dataSheet->getStyle('D1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('E')->setAutoSize(true);
            $dataSheet->setCellValue('E1', 'Район');
            $dataSheet->getStyle('E1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('F')->setAutoSize(true);
            $dataSheet->setCellValue('F1', 'Номенклатура');
            $dataSheet->getStyle('F1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('G')->setAutoSize(true);
            $dataSheet->setCellValue('G1', 'Цена');
            $dataSheet->getStyle('G1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('H')->setAutoSize(true);
            $dataSheet->setCellValue('H1', 'Кол-во');
            $dataSheet->getStyle('H1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('I')->setAutoSize(true);
            $dataSheet->setCellValue('I1', 'Итого колво');
            $dataSheet->getStyle('I1')->applyFromArray($head_style);
            $dataSheet->getColumnDimension('J')->setAutoSize(true);
            $dataSheet->setCellValue('J1', 'Итого сумма');
            $dataSheet->getStyle('J1')->applyFromArray($head_style);

        }
        return $sheets[$idx];
    }

    protected function _exportOrder(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $dataSheet, &$row, Order $order)
    {
        static $regions = null;
        if ($regions === null) {
            $regions = Region::getList();
        }
        static $style = null;
        if ($style === null) {
            $style = $this->_getRowStyle();
        }
        foreach ($order->products as $product) {
            $row++;

            $dataSheet->setCellValue('A' . $row, $order->number);
            $dataSheet->getStyle('A' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('B' . $row, $order->date);
            $dataSheet->getStyle('B' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('C' . $row, $order->client);
            $dataSheet->getStyle('C' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('D' . $row, $order->phone);
            $dataSheet->getStyle('D' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('E' . $row, $regions[$order->region_id] ?? $order->region_id);
            $dataSheet->getStyle('E' . $row)->applyFromArray($style);

            $dataSheet->setCellValue('F' . $row, $product->name);
            $dataSheet->getStyle('F' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('G' . $row, $product->price);
            $dataSheet->getStyle('G' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('H' . $row, $product->q);
            $dataSheet->getStyle('H' . $row)->applyFromArray($style);

            $dataSheet->setCellValue('I' . $row, $order->q);
            $dataSheet->getStyle('I' . $row)->applyFromArray($style);
            $dataSheet->setCellValue('J' . $row, $order->sum);
            $dataSheet->getStyle('J' . $row)->applyFromArray($style);
        }
    }
    public function actionExport()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $tmpPath = \Yii::getAlias('@runtime/excel/');
        \yii\helpers\FileHelper::createDirectory($tmpPath);

        $sheet = $this->getSheet($xls);
        $q = Order::findActive();
        $row = 1;
        foreach($q->each(1) as $order) {
            $this->_exportOrder($sheet, $row, $order);
        }

        // Save
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($xls, 'Xls');
        $file = microtime().'.xls';
        $objWriter->save($tmpPath.$file);

        //header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"" . 'Export_' . date('Y-m-d_H-i-s') . '.xls' . "\"" );
        header ("Content-Description: PHP/INTERBASE Generated Data" );
        readfile($tmpPath.$file);
        sleep(1);
        @unlink($tmpPath.$file);
        die;
    }
}
