<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-datecontrol
 * @version 1.2.0
 */

namespace kartik\datecontrol;

use Yii;

/**
 * Asset bundle for PHP Date Formatter
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateFormatterAsset extends \kartik\widgets\AssetBundle
{
    
    public function init()
    {
        $this->setSourcePath('@vendor/kartik-v/php-date-formatter');
        $this->setupAssets('js', ['js/php-date-formatter']);
        parent::init();
    }

}