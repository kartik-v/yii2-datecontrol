<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-datecontrol
 * @version 1.5.0
 */

namespace kartik\datecontrol\controllers;

use DateTime;
use DateTimeZone;
use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use kartik\datecontrol\Module;

class ParseController extends \yii\web\Controller
{

    /**
     * Convert display date for saving to model
     *
     * @returns JSON encoded HTML output
     */
    public function actionConvert()
    {
        $output = '';
        $module = Yii::$app->controller->module;
        $post = Yii::$app->request->post();
        if (isset($post['displayDate'])) {
            $type = empty($post['type']) ? Module::FORMAT_DATE : $post['type'];
            $saveFormat = ArrayHelper::getValue($post, 'saveFormat');
            $dispFormat = ArrayHelper::getValue($post, 'dispFormat');
            $dispTimezone = ArrayHelper::getValue($post, 'dispTimezone');
            $saveTimezone = ArrayHelper::getValue($post, 'saveTimezone');
            if ($dispTimezone != null) {
                $date = DateTime::createFromFormat($dispFormat, $post['displayDate'], new DateTimeZone($dispTimezone));
            } else {
                $date = DateTime::createFromFormat($dispFormat, $post['displayDate']);
            }
            if (empty($date) || !$date) {
                $value = '';
            } elseif ($saveTimezone != null) {
                $value = $date->setTimezone(new DateTimeZone($saveTimezone))->format($saveFormat);
            } else {
                $value = $date->format($saveFormat);
            }
            echo Json::encode(['status' => 'success', 'output' => $value]);
        } else {
            echo Json::encode(['status' => 'error', 'output' => 'No display date found']);
        }
    }
    
}
