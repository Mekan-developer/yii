<?php

namespace common\grid;

use yii\grid\DataColumn;
use common\models\User;

class UserColumn extends DataColumn
{
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $userId = $model->__get($this->attribute);
        if (empty($userId)) {
            return '-';
        }

        if (!isset(self::$cache[$userId])) {
            $userModel = User::findOne($userId);
            if (!$userModel) {
                return '-';
            }
            self::$cache[$userId] = $userModel->getFullName();
        }
        return self::$cache[$userId];
    }
}
