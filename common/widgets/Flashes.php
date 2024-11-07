<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\widgets;

use Yii;

class Flashes extends \yii\base\Widget
{
    public $options = [];

    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning',
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    public function init()
    {
        parent::init();

//        // TODO Check is it needed ?
//        $showFlashes = (bool)@Yii::$app->params['showFlashes'];
//        if(!$showFlashes) return '';

        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array)$data;
                if(sizeof($data) > 1) {
                    $message = [];
                    $i = 1;
                    foreach($data as $item) {
                        $message[] = $i . '. ' . $item;
                        $i++;
                    }
                    $message = implode("<br />\r\n", (array)$message);
                } else {
                    $message = $data[0];
                }

                $this->options['class'] = $this->alertTypes[$type] . $appendCss;
                $this->options['id']    = $this->getId() . '-' . $type;

                echo Alert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => $this->options,
                ]);

                $session->removeFlash($type);
            }
        }
    }

    public static function setError($message)
    {
        \Yii::$app->session->addFlash('error', $message);
    }

    public static function setSuccess($message)
    {
        \Yii::$app->session->addFlash('success', $message);
    }

    public static function setWarning($message)
    {
        \Yii::$app->session->addFlash('warning', $message);
    }

    public static function setInfo($message)
    {
        \Yii::$app->session->addFlash('info', $message);
    }
}
