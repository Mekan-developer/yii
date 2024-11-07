<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use Yii;

class ReportSearch extends Model
{
    public $period;
    public $region_id;

    public function rules()
    {
        return [
            [['period', 'region_id'], 'safe'],
        ];
    }

    public function search($params)
    {
        $this->load($params);

        // Build the base SQL query to fetch data
        $sql = "
            SELECT 
                o.client AS client_name,
                p.id AS product_id,
                p.name AS product_name,
                op.q AS quantity
            FROM orders o
            LEFT JOIN orders_products op ON o.id = op.order_id
            LEFT JOIN products p ON op.product_id = p.id
            WHERE o.is_deleted = 0 AND op.is_deleted = 0 AND p.is_deleted = 0
        ";

        // Apply filters to the SQL query
        $conditions = [];
        $params = [];
        
        if ($this->period) {
            $conditions[] = 'o.date = :period';
            $params[':period'] = $this->period;
        }

        if ($this->region_id) {
            $conditions[] = 'o.region_id = :region_id';
            $params[':region_id'] = $this->region_id;
        }

        if (!empty($conditions)) {
            $sql .= ' AND ' . implode(' AND ', $conditions);
        }

        // Execute the query and fetch all results
        $orders = Yii::$app->db->createCommand($sql, $params)->queryAll();

        // Format data for display in the report
        $reportData = [];

        // Populate the report data structure
        foreach ($orders as $order) {
            $clientName = $order['client_name'];
            $productId = $order['product_id'];
            $quantity = $order['quantity'] ?? 0;

            // Initialize client's row if it doesn't exist
            if (!isset($reportData[$clientName])) {
                $reportData[$clientName] = array_fill(1, 12, 0); // 12 products with default quantity 0
            }

            // Set the quantity for the specific product
            $reportData[$clientName][$productId] = $quantity;
        }

        // Convert the structured data into an ArrayDataProvider
        return new ArrayDataProvider([
            'allModels' => $reportData,
            'pagination' => false,
        ]);
    }
}
