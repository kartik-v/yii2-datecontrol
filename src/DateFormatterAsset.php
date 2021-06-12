<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   1.9.8
 */

namespace kartik\datecontrol;

use kartik\base\AssetBundle;

/**
 * Asset bundle for the [Krajee PHP Date Formatter](http://plugins.krajee.com/php-date-formatter) extension.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateFormatterAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@vendor/kartik-v/php-date-formatter');
        $this->setupAssets('js', ['js/php-date-formatter']);
        parent::init();
    }
}