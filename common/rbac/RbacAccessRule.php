<?php

namespace common\rbac;

use Yii;
use common\helpers\Access;

/**
 * Modified RBAC access rule
 * Allows to use $ sign, to check rights
 * {@inheritdoc}
 */
class RbacAccessRule extends \yii\filters\AccessRule
{
    /**
     * {@inheritdoc}
     */
    public function allows($action, $user, $request)
    {
        if ($this->matchAction($action)
            && $this->matchRole($user, $action)
            && $this->matchIP($request->getUserIP())
            && $this->matchVerb($request->getMethod())
            && $this->matchController($action->controller)
            && $this->matchCustom($action)
        ) {
            return $this->allow ? true : false;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function matchRole($user, $action = '')
    {
        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role === '?') { // Must be not logged in
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') { // Must be logged in
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '$') { // Must have needed rights for current action
                $modulePrefix = '';
                $skipAppId = (property_exists(Yii::$app->urlManager, 'skipAppId') && Yii::$app->urlManager->skipAppId);
                if (!$skipAppId || $action->controller->module->id != \Yii::$app->id) $modulePrefix = $action->controller->module->id . '/';
                if (!$user->getIsGuest() && Access::checkAccess('$', $modulePrefix . $action->controller->id . '/' . $action->id)) {
                    return true;
                }
            } elseif ($user->can($role)) { // Must have selected role / be in group / have selected right
                return true;
            }
        }

        return false;
    }
}
