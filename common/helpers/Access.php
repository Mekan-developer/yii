<?php

namespace common\helpers;

use common\models\User;
use Yii;

class Access extends \yii\helpers\Url
{
    public static $adminAccess = [
        'backend/products',
        'backend/orders',
        'backend/regions',
        'backend/users',
        'backend/report-dev',
        'backend/report-demo',
        'backend/report-upload',
    ];

    public static function userCan($url)
    {
        static $list = [];
        if (!isset($list[$url])) {
            $list[$url] = Access::checkAccess('$', $url); //Yii::$app->user->can($url);
        }
        return $list[$url];
    }

    /**
     * Checks user access to url
     * @param string $access
     * @param array  $url
     * @return bool
     */
    public static function checkAccess($access, $url)
    {
        if ($access) {
            if ($access == '@') {
                if (Yii::$app->user->isGuest) {
                    return false;
                }
            } elseif ($access == '?') {
                if (!Yii::$app->user->isGuest) {
                    return false;
                }
            } else {
                $stringUrl = is_string($url) ? $url : static::ActionByUrl($url);
                $identity = \Yii::$app->user->identity;
                if ($stringUrl == 'backend/users/sign-in' || $stringUrl == 'backend/cleanup/index') {
                    return ($identity->id == 1);
                }
                $userType = (int)$identity->type;
                $ok = false;
                $allowed = [];
                if ($userType == User::USER_TYPE_ADMIN) {
                    $allowed = static::$adminAccess;
                }
                foreach($allowed as $line) {
                    if (strpos($stringUrl, $line) === 0) {
                        $ok = true;
                        break;
                    }
                }
                return $ok;
            }
        }
        return true;
    }

    /**
     * Returns string action name like `frontend/page/about`
     * by url array
     * @param array $url
     * @return string
     */
    public static function actionByUrl($url)
    {
        if (!is_array($url)) {
            return '';
        }

        $skipAppId = (property_exists(Yii::$app->urlManager, 'skipAppId') && Yii::$app->urlManager->skipAppId);
        $url = $url[0];
        $parts = explode('/', $url);
        if (sizeof($parts) == 4 && empty($parts[0])) {       //    /module/controller/action
            array_shift($parts);                      //    /module/controller/action -> module/controller/action
            if ($skipAppId && $parts[0] == Yii::$app->id) {  //   APP/controller/action -> controller/action
                array_shift($parts);
                return implode('/', $parts);
            }
            return implode('/', $parts);
        } elseif (sizeof($parts) == 3 && empty($parts[0])) { //    /controller/action OR /module/controller
            $module = Yii::$app->getModule($parts[1]);
            if ($module) {                                   //    /module/controller
                array_shift($parts);                  //    /module/controller -> module/controller
                array_push($parts, 'index');    //    module/controller -> module/controller/index
                return implode('/', $parts);
            } elseif ($skipAppId && $parts[1] == Yii::$app->id) {
                array_shift($parts);                  //    /APP/controller -> APP/controller
                array_shift($parts);                  //    APP/controller -> controller
                array_push($parts, 'index');    //    controller -> controller/index
                return implode('/', $parts);
            }
                                                             //    /controller/action
            if (!$skipAppId) $parts[0] = Yii::$app->id;      //    /controller/action -> APP/controller/action
            else unset($parts[0]);                           //    /controller/action -> controller/action
            return implode('/', $parts);
        } elseif (sizeof($parts) == 3) {                     //    module/controller/action
            if ($skipAppId && $parts[0] == Yii::$app->id) {  //    APP/controller/action
                array_shift($parts);                  //    APP/controller/action -> controller/action
                return implode('/', $parts);
            }
            return $url;
        } elseif (sizeof($parts) == 2 && empty($parts[0])) { //    / OR /controller
            if (!$skipAppId) $parts[0] = Yii::$app->id;      //    APP/controller
            else unset($parts[0]);                           //    controller
            if (empty($parts[1])) {                          //     -> site
                $parts[1] = 'site';
            }
            array_push($parts, 'index');        //     controller -> controller/index
            return implode('/', $parts);
        } elseif (sizeof($parts) == 2) {                     //    controller/action
            if (!$skipAppId) array_unshift($parts, Yii::$app->id); //    controller/action -> APP/controller/action
            return implode('/', $parts);
        } elseif (empty($parts[0])) {
            if (!$skipAppId) return Yii::$app->id . '/site/index'; // -> APP/site/index
            return 'site/index';                                   // -> site/index
        }
        //    action
        array_unshift($parts, Yii::$app->controller->id);   //    action -> controller/action
        if (!$skipAppId || Yii::$app->controller->module->id != \Yii::$app->id) {
            array_unshift($parts, Yii::$app->controller->module->id); //    controller/action -> APP/controller/action
        }
        return implode('/', $parts);
    }
}
