<?php

namespace backend\controllers;

use common\controllers\RefController;
use common\models\Order;
use Yii;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use backend\models\Orders;
use backend\models\OrdersProducts;
use backend\models\Regions;

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

    public function actionIndex()
    {
        // Fetch filter parameters from the GET request
        $startDate = Yii::$app->request->get('start_date');
        $endDate = Yii::$app->request->get('end_date');
        $regionId = Yii::$app->request->get('region_id');

        // Build query for orders with filters
        $query = Order::find()
            ->alias('o')
            ->joinWith('ordersProducts op')
            ->where(['o.is_deleted' => 0]);

        if ($startDate && $endDate) {
            $query->andWhere(['between', 'o.date', $startDate, $endDate]);
        }

        if ($regionId) {
            $query->andWhere(['o.region_id' => $regionId]);
        }

        // Execute query and fetch results
        $orders = $query->all();

        // Prepare data structure for report view
        $reportData = [];
        $totals = array_fill(0, 12, 0); // Initialize totals for 12 products

        foreach ($orders as $order) {
            $clientData = [
                'client' => $order->client,
                'products' => array_fill(0, 12, 0) // Initialize products columns with 0s
            ];

            foreach ($order->ordersProducts as $product) {
                $productIndex = $product->product_id - 1; // Assuming product_id starts from 1 to 12
                if ($productIndex >= 0 && $productIndex < 12) {
                    $clientData['products'][$productIndex] = $product->q;
                    $totals[$productIndex] += $product->q;
                }
            }

            $reportData[] = $clientData;
        }

        return $this->render('index', [
            'reportData' => $reportData,
            'totals' => $totals,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'regionId' => $regionId,
        ]);
    }
}
