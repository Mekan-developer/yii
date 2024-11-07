<?php

namespace common\rbac;

use Yii;
use common\models\User;

/**
 * AccessControl provides simple access control based on a set of rules.
 *
 * AccessControl is an action filter. It will check its [[rules]] to find
 * the first rule that matches the current context variables (such as user IP address, user role).
 * The matching rule will dictate whether to allow or deny the access to the requested controller
 * action. If no rule matches, the access will be denied.
 *
 * To use AccessControl, declare it in the `behaviors()` method of your controller class.
 * For example, the following declarations will allow authenticated users to access the "create"
 * and "update" actions and deny all other users from accessing these two actions.
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'access' => [
 *             'class' => \yii\filters\AccessControl::className(),
 *             'only' => ['create', 'update'],
 *             'rules' => [
 *                 // deny all POST requests
 *                 [
 *                     'allow' => false,
 *                     'verbs' => ['POST']
 *                 ],
 *                 // allow authenticated users
 *                 [
 *                     'allow' => true,
 *                     'roles' => ['@'],
 *                 ],
 *                 ],
 *                 // check users rights
 *                 [
 *                     'allow' => true,
 *                     'roles' => ['$'],
 *                 ],
 *                 // everything else is denied
 *             ],
 *         ],
 *     ];
 * }
 * ```
 */
class RbacAccessControl extends \yii\filters\AccessControl
{
    /**
     * {@inheritdoc}
     */
    public $ruleConfig = ['class' => 'common\rbac\RbacAccessRule'];
    
    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();

        /* @var \app\models\User $user */
        $user = $this->user->identity;
        if ($user == null || $user->is_deleted) {
            return false;
        }
    }
}
