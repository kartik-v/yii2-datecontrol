<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   1.9.3
 */

namespace kartik\datecontrol\controllers;

use DateTimeZone;
use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use kartik\datecontrol\Module;
use kartik\datecontrol\DateControl;

class ParseController extends \yii\web\Controller
{

    /**
     * Convert display date for saving to model
     *
     * @return string JSON encoded HTML output
     */
    public function actionConvert()
    {
        $output = '';
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if (isset($post['displayDate'])) {
            $type = empty($post['type']) ? Module::FORMAT_DATE : $post['type'];
            $saveFormat = ArrayHelper::getValue($post, 'saveFormat');
            $dispFormat = ArrayHelper::getValue($post, 'dispFormat');
            $dispTimezone = ArrayHelper::getValue($post, 'dispTimezone');
            $saveTimezone = ArrayHelper::getValue($post, 'saveTimezone');
            $settings = ArrayHelper::getValue($post, 'settings', []);
            $dispDate = ArrayHelper::getValue($post, 'displayDate');
            $date = DateControl::getTimestamp($type, $dispDate, $dispFormat, $dispTimezone, $settings);
            if (empty($date) || !$date) {
                $value = '';
            } elseif ($saveTimezone != null) {
                $value = $date->setTimezone(new DateTimeZone($saveTimezone))->format($saveFormat);
            } else {
                $value = $date->format($saveFormat);
            }
            return ['status' => 'success', 'output' => $value];
        } else {
            return ['status' => 'error', 'output' => 'No display date found'];
        }
    }
}