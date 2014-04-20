<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-datecontrol
 * @version 1.0.0
 */

namespace kartik\datecontrol\controllers;

use Yii;
use yii\helpers\Json;
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
        if (isset($_POST['displayDate'])) {
            $type = empty($_POST['type']) ? Module::FORMAT_DATE : $_POST['type'];
            $saveFormat = $_POST['saveFormat'];
            $dispFormat = $_POST['dispFormat'];
            $date = \DateTime::createFromFormat($dispFormat, $_POST['displayDate']);
            $value = (empty($date) || !$date) ? '' :  $date->format($saveFormat);
            echo Json::encode(['status' => 'success', 'output' => $value]);
        } else {
            echo Json::encode(['status' => 'error', 'output' => 'No display date found']);
        }
    }

}