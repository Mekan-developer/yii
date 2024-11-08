<?php

namespace backend\controllers;

use backend\models\ReportSearch;
use common\controllers\RefController;
use common\models\OrderProduct;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\web\Response;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use common\models\Region;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Border;







class ReportDevController extends RefController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

     public function actionReport()
    {
        // Instantiate the search model for the report
        $searchModel = new ReportSearch();

        // Fetch data based on search parameters (filters)
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // Render the main index.php located directly in backend/views/
        return $this->render('/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

 

    


    public function actionExportExcel()
    {
        // Получаем данные фильтров
        $searchModel = new ReportSearch();
        $searchModel->load(\Yii::$app->request->post());
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $orders = $dataProvider->getModels();
    
        // Создаем новый документ Excel
        $spreadsheet = new Spreadsheet();
    
        // Настраиваем лист
        $sheet = $this->getSheet($spreadsheet, 0, 'Отчёт');
    
        // Получаем данные для заголовков
        $products = Product::find()->select(['id', 'name'])->orderBy('id')->all();
        $period = $searchModel->period ?: 'Все периоды';
        $region = $searchModel->region_id ? Region::findOne($searchModel->region_id)->name : 'Все районы';
    
        // Вставляем фильтры
        $sheet->setCellValue('B2', 'Период:');
        $sheet->setCellValue('C2', $period);
        $sheet->setCellValue('B3', 'Район:');
        $sheet->setCellValue('C3', $region);
    
        // Настраиваем заголовки
        $sheet->setCellValue('A5', 'ФИО клиента');
        $column = 'B';
    
        foreach ($products as $product) {
            $sheet->setCellValue($column . '5', $product->name);
            $column++;
        }
        $sheet->setCellValue($column . '5', 'ИТОГО');
    
        // Применяем стили заголовков
        $sheet->getStyle('A5:' . $column . '5')->applyFromArray($this->_getHeaderStyle());
    
        // Заполняем данные
        $row = 6;
        $totalByProduct = array_fill(0, count($products), 0);
        $grandTotal = 0;
    
        foreach ($orders as $order) {
            $sheet->setCellValue("A{$row}", $order->client);
            $column = 'B';
            $total = 0;
    
            foreach ($products as $index => $product) {
                $orderProduct = OrderProduct::find()
                    ->where(['order_id' => $order->id, 'product_id' => $product->id])
                    ->one();
    
                $quantity = $orderProduct ? $orderProduct->q : 0;
                $sheet->setCellValue("{$column}{$row}", $quantity);
                $total += $quantity;
                $totalByProduct[$index] += $quantity;
                $column++;
            }
    
            $sheet->setCellValue("{$column}{$row}", $total);
            $grandTotal += $total;
            $row++;
        }
    
        // Итоговая строка
        $sheet->setCellValue("A{$row}", 'ИТОГО');
        $column = 'B';
    
        foreach ($totalByProduct as $total) {
            $sheet->setCellValue("{$column}{$row}", $total);
            $column++;
        }
        $sheet->setCellValue("{$column}{$row}", $grandTotal);
    
        // Стилизация итоговой строки
        $sheet->getStyle("A{$row}:" . $column . "{$row}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'F8D7DA'],
            ],
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    
        // Авторазмер колонок
        foreach (range('A', $column) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Отправляем Excel-файл пользователю
        $filename = 'Отчёт_' . date('Y-m-d_H-i-s') . '.xlsx';
        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $headers = $response->getHeaders();
        $headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $headers->set('Cache-Control', 'max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        $response->content = $content;
    
        return $response;
    }
    
    // Метод getSheet
    protected function getSheet($spreadsheet, $index = 0, $title = 'Данные')
    {
        static $sheets = [];
    
        if (!isset($sheets[$index])) {
            if ($index === 0) {
                $sheets[$index] = $spreadsheet->getActiveSheet();
                $sheets[$index]->setTitle($title);
            } else {
                $sheets[$index] = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $title);
                $spreadsheet->addSheet($sheets[$index], $index + 1);
            }
    
            $sheet = $sheets[$index];
    
            // Настройки страницы
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageMargins()->setLeft(0.1)->setRight(0.1)->setTop(0.1)->setBottom(0.1);
    
            // Авторазмер колонок
            foreach (range('A', 'Z') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
    
        return $sheets[$index];
    }
    
    // Метод для получения стиля заголовков
    protected function _getHeaderStyle()
    {
        return [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD700'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
    }
    
    

  

    public function actionIndex()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
