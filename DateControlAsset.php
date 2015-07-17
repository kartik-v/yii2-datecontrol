<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   1.9.3
 */

namespace kartik\datecontrol;

use Yii;

/**
 * Asset bundle for DateControl Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateControlAsset extends \kartik\base\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'kartik\datecontrol\DateFormatterAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/datecontrol']);
        parent::init();
    }
}