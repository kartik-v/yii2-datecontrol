<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version   1.9.1
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
            $dispDate = $post['displayDate'];
            /**
             * Fix to prevent DateTime defaulting the time
             * part to current time, for FORMAT_DATE
             */
            if ($type == Module::FORMAT_DATE) {
                $dispDate .= " 00:00:00";
                $dispFormat .= " H:i:s";
            }
            if ($dispTimezone != null) {
                $date = DateTime::createFromFormat($dispFormat, $dispDate, new DateTimeZone($dispTimezone));
            } else {
                $date = DateTime::createFromFormat($dispFormat, $dispDate);
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
