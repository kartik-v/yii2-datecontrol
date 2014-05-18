<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-datecontrol
 * @version 1.0.0
 */

namespace kartik\datecontrol;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\View;
use yii\web\JsExpression;

/**
 * DateControl widget enables you to control the formatting of date/time separately in View (display) and Model (save).
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateControl extends \kartik\widgets\InputWidget
{
    const FORMAT_DATE = 'date';
    const FORMAT_TIME = 'time';
    const FORMAT_DATETIME = 'datetime';

    /**
     * @var string data type to use for the displayed date control. One of the FORMAT constants.
     */
    public $type = self::FORMAT_DATE;

    /**
     * @var string the format string for displaying the date. If not set, will automatically use the settings
     * from the Module based on the `type` setting.
     */
    public $displayFormat;

    /**
     * @var string the default format string to be save the date as. If not set, will automatically use the settings
     * from the Module.
     */
    public $saveFormat;

    /**
     * @var bool whether to automatically use \kartik\widgets based on `$type`. Will use these widgets:
     * - \kartik\widgets\DatePicker for FORMAT_DATE
     * - \kartik\widgets\TimePicker for FORMAT_TIME
     * - \kartik\widgets\DateTimePicker for FORMAT_DATETIME
     * If not set, this will default to `true.`
     */
    public $autoWidget;

    /**
     * @var string any custom widget class to use. Will only be used if autoWidget is set to `false`.
     */
    public $widgetClass;

    /**
     * @var array the HTML attributes for the display input. If a widget is used based on `autoWidget` or `widgetClass`,
     * this will be considered as the widget options.
     */
    public $options = [];

    /**
     * @var array the HTML attributes for the base model input that will be saved typically to database.
     * The following special options are recognized:
     * - 'type': string, whether to generate a 'hidden' or 'text' input. Defaults to 'hidden'.
     * - 'label': string, any label to be placed before the input. Will be only displayed if 'type' is 'text'.
     */
    public $saveOptions = [];

    /**
     * @var string display attribute name
     */
    protected $_displayAttribName;

    /**
     * @var \kartik\datecontrol\Module the `datecontrol` module instance
     */
    protected $_module;

    /**
     * @var array the parsed widget settings from the module
     */
    protected $_widgetSettings = [];

    /**
     * Initializes widget
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->initModule();
        parent::init();
        $this->_displayAttribName = (($this->hasModel()) ? $this->attribute : $this->name) . '-' . $this->options['id'];
        $this->saveOptions['id'] = $this->options['id'] . '-save';
    }

    /**
     * Initializes widget based on module settings
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function initModule()
    {
        $this->_module = Yii::$app->getModule('datecontrol');
        if (empty($this->_module)) {
            throw new InvalidConfigException("The module 'datecontrol' has not been setup in your configuration file.");
        }
        if (!isset($this->autoWidget)) {
            $this->autoWidget = $this->_module->autoWidget;
        }
        if (empty($this->autoWidget)) {
            $this->autoWidget = true;
        }
        if (!$this->autoWidget && !empty($this->widgetClass) && !class_exists($this->widgetClass)) {
            throw new InvalidConfigException("The widgetClass '{$this->widgetClass}' entered is invalid.");
        }

        if (empty($this->displayFormat) && empty($this->_module->displaySettings[$this->type])) {
            $attrib = $this->type . 'Format';
            $this->displayFormat = isset(Yii::$app->formatter->$attrib) ? Yii::$app->formatter->$attrib : 'd-M-Y';
        } elseif (empty($this->displayFormat)) {
            $this->displayFormat = $this->_module->displaySettings[$this->type];
        }

        if (empty($this->saveFormat)) {
            $this->saveFormat = $this->_module->saveSettings[$this->type];
        }

            $this->_widgetSettings = $this->_module->widgetSettings;
        if ($this->autoWidget) {
            $this->_widgetSettings = [
                self::FORMAT_DATE => ['class' => '\kartik\widgets\DatePicker'],
                self::FORMAT_TIME => ['class' => '\kartik\widgets\TimePicker'],
                self::FORMAT_DATETIME => ['class' => '\kartik\widgets\DateTimePicker'],
            ];
            foreach ($this->_widgetSettings as $type => $setting) {
                if (empty($setting['class']) || !class_exists($setting['class'])) {
                    $message = empty($setting['class']) ? "No class was setup for the key '{$type}'." :
                        "The class '" . $setting['class'] . "' setup for key '{$type} is invalid.";
                    throw new InvalidConfigException('Invalid widgetSettings in Date Control module config. ' . $message);
                }
                $this->_widgetSettings[$type]['options'] = Module::getWidgetOptions($type);
            }
        }
    }

    /**
     * Runs widget
     *
     * @return string|void
     */
    public function run()
    {
        $this->registerAssets();
        echo $this->getDisplayInput() . $this->getSaveInput();
        parent::run();
    }

    /**
     * Whether a widget is used to render the display
     *
     * @return bool
     */
    protected function isWidget()
    {
        return ($this->autoWidget || !empty($this->widgetClass));
    }

    /**
     * Generates the display input
     *
     * @return string
     */
    protected function getDisplayInput()
    {
        $value = empty($this->value) ? '' : $this->getDisplayValue($this->value);
        if (!$this->isWidget()) {
            if (empty($this->options['class'])) {
                $this->options['class'] = 'form-control';
            }
            return Html::textInput($this->_displayAttribName, $value, $this->options);
        }
        $class = (!$this->autoWidget && !empty($this->widgetClass)) ? $this->widgetClass :
            ArrayHelper::getValue($this->_widgetSettings[$this->type], 'class', '\yii\jui\DatePicker');

        if (!empty($this->displayFormat) && $this->autoWidget) {
            $this->options += Module::defaultWidgetOptions($this->type, $this->displayFormat);
        }
        if (!empty($this->_widgetSettings[$this->type]['options'])) {
            $this->options += $this->_widgetSettings[$this->type]['options'];
        }
        unset($this->options['model'], $this->options['attribute']);
        $this->options['name'] = $this->_displayAttribName;
        $this->options['value'] = $value;
        return $class::widget($this->options);
    }

    /**
     * Generates the save input
     *
     * @return string
     */
    protected function getSaveInput()
    {
        $type = ArrayHelper::remove($this->saveOptions, 'type', 'hidden');
        $label = ArrayHelper::remove($this->saveOptions, 'label', '');

        if ($type === 'text') {
            $this->saveOptions['tabindex'] = 10000;
            return $label . ($this->hasModel() ?
                Html::activeTextInput($this->model, $this->attribute, $this->saveOptions) :
                Html::textInput($this->name, $this->value, $this->saveOptions));
        }
        return $this->hasModel() ?
            Html::activeHiddenInput($this->model, $this->attribute, $this->saveOptions) :
            Html::hiddenInput($this->name, $this->value, $this->saveOptions);
    }

    /**
     * Gets the formatted display date value
     *
     * @param $data the input date data
     * @return string
     */
    protected function getDisplayValue($data)
    {
        return Yii::$app->formatter->format($data, [$this->type, $this->displayFormat]);
    }

    /**
     * Registers assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DateControlAsset::register($view);
        $this->pluginOptions = [
            'idDisp' => $this->options['id'],
            'idSave' => $this->saveOptions['id'],
            'url' => Url::to([$this->_module->convertAction]),
            'type' => $this->type,
            'saveFormat' => $this->saveFormat,
            'dispFormat' => $this->displayFormat,
        ];
        $this->registerPluginOptions('datecontrol');
        if ($this->isWidget()) {
            unset($this->options['data-plugin-name'], $this->options['data-plugin-options']);
        }
        $view->registerJs("parseDateControl({$this->_hashVar});");
    }
}