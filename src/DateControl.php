<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   1.9.8
 */

namespace kartik\datecontrol;

use DateTime;
use DateTimeZone;
use Exception;
use kartik\base\Config;
use kartik\base\InputWidget;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * DateControl widget enables you to the formatting of date/time separately for display (View) and saving to
 * database (Model).
 *
 * Usage example:
 *
 * ~~~
 * use kartik\datecontrol\DateControl;
 * // usage of rendering date control widget as an active field within Yii active form
 * echo $form->field($model, 'datetime_2')->widget(DateControl::class, [
 *     'displayFormat' => 'php:d-M-Y H:i:s',
 *     'type' => DateControl::FORMAT_DATETIME
 * ]);
 * ~~~
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateControl extends InputWidget
{
    /**
     * Date only format type.
     */
    const FORMAT_DATE = 'date';
    /**
     * Time only format type.
     */
    const FORMAT_TIME = 'time';
    /**
     * Date and time format type.
     */
    const FORMAT_DATETIME = 'datetime';
    /**
     * @var array the english date settings.
     */
    private static $_enSettings = [
        'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'daysShort' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        'months' => [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ],
        'monthsShort' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        'meridiem' => ['AM', 'PM'],
    ];
    /**
     * @var string data type to use for the displayed date control. One of the FORMAT constants.
     */
    public $type = self::FORMAT_DATE;

    /**
     * @var boolean whether to use ajaxConversion to process date format for the widget.
     */
    public $ajaxConversion;

    /**
     * @var string the format string for displaying the date. If not set, will automatically use the settings from the
     * [[Module]] based on the [[type]] property.
     */
    public $displayFormat;

    /**
     * @var string the default format string to be save the date as. If not set, will automatically use the settings
     * from the [[Module]].
     */
    public $saveFormat;

    /**
     * @var string the timezone for the displayed date. If not set, no timezone setting will be applied for formatting.
     * @see http://php.net/manual/en/timezones.php
     */
    public $displayTimezone;

    /**
     * @var string the timezone for the saved date. If not set, no timezone setting will be applied for formatting.
     * @see http://php.net/manual/en/timezones.php
     */
    public $saveTimezone;

    /**
     * @var boolean whether to automatically use \kartik\widgets based on `$type`. Will use these widgets:
     *
     * - [[\kartik\date\DatePicker]] when [[type]] is set to [[FORMAT_DATE]]
     * - [[\kartik\time\TimePicker]] when [[type]] is set to [[FORMAT_TIME]]
     * - [[\kartik\datetime\DateTimePicker]] when [[type]] is set to [[FORMAT_DATETIME]]
     *
     * If this property is not set, this will default to `true.`
     */
    public $autoWidget;

    /**
     * @var string any custom widget class to use. Will only be used if [[autoWidget]] is set to `false`.
     */
    public $widgetClass;

    /**
     * @var array the configuration options for the widget which will be parsed only in one of the following cases:
     *
     * - when [[autoWidget]] is `true` and this corresponds to widget settings for `DatePicker`, `TimePicker`, or
     *   `DateTimePicker` based on the [[$type]] setting, OR
     * - when [[autoWidget]] is `false` and [[widgetClass]] is set and this allows to set the configuration options for
     *   the particular widget class.
     */
    public $widgetOptions = [];

    /**
     * @var array the HTML attributes for the display input. This property is applicable and parsed only if
     * [[autoWidget]] is `false` and [[widgetClass]] is empty or not set. For a widget, the [[widgetOptions]] must be
     * used to configure the widget settings.
     */
    public $options = [];

    /**
     * @var array the HTML attributes for the base model input that will be saved typically to database. The following
     * special options are recognized:
     * - `type`: _string_, whether to generate a 'hidden' or 'text' input. Defaults to 'hidden'.
     * - `label`: _string_, any label to be placed before the input. Will be only displayed if 'type' is 'text'.
     */
    public $saveOptions = [];

    /**
     * @var boolean whether to fire an asynchronous ajax request. Defaults to `true`. You can set this to `false` for
     * cases, where you need this to be fired synchronously. For example when using this widget as a filter in
     * [[\kartik\grid\GridView]].
     */
    public $asyncRequest = true;
    /**
     * @inheritdoc
     */
    public $pluginName = 'datecontrol';
    /**
     * @var Module the `datecontrol` module instance.
     */
    protected $_module;
    /**
     * @var array the parsed widget class settings for each type (defaults from the module setting if not set).
     */
    protected $_widgetSettings = [];
    /**
     * @var boolean whether translation is needed
     */
    private $_doTranslate = false;

    /**
     * Parses and normalizes a date source and converts it to a [[DateTime]] object by parsing it based on specified
     * format.
     *
     * @param string $source the date source pattern.
     * @param string $format the date format.
     * @param string $timezone the date timezone.
     * @param array|string $settings the locale/language date settings.
     *
     * @return DateTime
     */
    public static function getTimestamp($source, $format, $timezone = null, $settings = [])
    {
        if (!isset($source) || $source === '') {
            return null;
        }
        $source = static::parseLocale($source, $format, $settings);
        if (substr($format, 0, 1) !== '!') {
            $format = '!' . $format;
        }
        if ($timezone != null) {
            $timestamp = DateTime::createFromFormat($format, $source, new DateTimeZone($timezone));
        } else {
            $timestamp = DateTime::createFromFormat($format, $source);
        }
        return $timestamp;
    }

    /**
     * Fetches the locale settings file.
     *
     * @param string $lang the locale/language ISO code.
     *
     * @return string the locale file name.
     */
    protected static function getLocaleFile($lang)
    {
        $s = DIRECTORY_SEPARATOR;
        $file = __DIR__ . "{$s}locales{$s}{$lang}{$s}dateSettings.php";
        if (!file_exists($file)) {
            $langShort = Config::getLang($lang);
            $file = __DIR__ . "{$s}locales{$s}{$langShort}{$s}dateSettings.php";
        }
        return $file;
    }

    /**
     * Parses locale data and returns an english format.
     *
     * @param string $source the date source pattern.
     * @param string $format the date format.
     * @param array|string $settings the locale/language date settings.
     *
     * @return string the converted date source to english.
     */
    protected static function parseLocale($source, $format, $settings = [])
    {
        if (empty($settings)) {
            return $source;
        }
        foreach (self::$_enSettings as $key => $value) {
            if (!empty($settings[$key]) && static::checkFormatKey($format, $key)) {
                $source = str_ireplace($settings[$key], $value, $source);
            }
        }
        return $source;
    }

    /**
     * Checks if the format string contains the relevant date format pattern based on the passed key.
     *
     * @param string $format the date format string
     * @param string $key the key to check
     *
     * @return boolean
     */
    protected static function checkFormatKey($format, $key)
    {
        switch ($key) {
            case 'months':
                return strpos($format, 'F') !== false;
            case 'monthsShort':
                return strpos($format, 'M') !== false;
            case 'days':
                return strpos($format, 'l') !== false;
            case 'daysShort':
                return strpos($format, 'D') !== false;
            case 'meridiem':
                return stripos($format, 'A') !== false;
            default:
                return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initConfig();
        if (!isset($this->ajaxConversion)) {
            $this->ajaxConversion = $this->_module->ajaxConversion;
        }
        if (!$this->ajaxConversion && ($this->displayTimezone != null || $this->saveTimezone != null)) {
            throw new InvalidConfigException(
                "You must set 'ajaxConversion' to 'true' when using time-zones for display or save."
            );
        }
        parent::init();
        $this->initLanguage();
        $this->setDataVar($this->pluginName);
        $this->saveOptions['id'] = $this->options['id'];
        $this->options['id'] .= '-disp';
        if ($this->isWidget()) {
            $this->widgetOptions['options']['id'] = $this->options['id'];
        }
        $this->_doTranslate = isset($this->language) && $this->language != 'en';
        if ($this->autoWidget && !isset($this->_widgetSettings[$this->type]['bsVersion']) && isset($this->bsVersion)) {
            $this->_widgetSettings[$this->type]['bsVersion'] = $this->bsVersion;
        }
        $this->initBsVersion();
        if ($this->_doTranslate && $this->autoWidget) {
            $this->_widgetSettings[$this->type]['options']['language'] = $this->language;
        }
        $this->setLocale();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerAssets();
        echo $this->getDisplayInput() . $this->getSaveInput();
        parent::run();
    }

    /**
     * Gets the formatted display date value.
     *
     * @param string $data the input date data.
     *
     * @return string
     */
    public function getDisplayValue($data)
    {
        $saveDate = $data;
        $saveFormat = $this->saveFormat;
        $settings = $this->_doTranslate ? ArrayHelper::getValue($this->pluginOptions, 'dateSettings', []) : [];
        $date = static::getTimestamp($saveDate, $saveFormat, $this->saveTimezone, $settings);
        if ($date && $date instanceof DateTime) {
            if ($this->displayTimezone != null) {
                $date->setTimezone(new DateTimeZone($this->displayTimezone));
            }
            if ($this->type === self::FORMAT_DATE) {
                $value = date($this->displayFormat, $date->getTimestamp());
            } else {
                $value = $date->format($this->displayFormat);
            }
            if ($this->_doTranslate) {
                $value = $this->translateDate($value, $this->displayFormat);
            }
            return $value;
        }
        return null;
    }

    /**
     * Initializes widget based on module settings.
     *
     * @throws InvalidConfigException
     */
    protected function initConfig()
    {
        $this->_module = Config::initModule(Module::class);
        if (!isset($this->autoWidget)) {
            $this->autoWidget = $this->_module->autoWidget;
        }
        if (!$this->autoWidget && !empty($this->widgetClass) && !class_exists($this->widgetClass)) {
            throw new InvalidConfigException("The widgetClass '{$this->widgetClass}' entered is invalid.");
        }
        if ($this->autoWidget === null) {
            $this->autoWidget = true;
        }
        $this->_widgetSettings = $this->_module->widgetSettings;
        if (empty($this->displayFormat)) {
            $this->displayFormat = $this->_module->getDisplayFormat($this->type);
        } else {
            $this->displayFormat = Module::parseFormat($this->displayFormat, $this->type);
        }
        if (empty($this->saveFormat)) {
            $this->saveFormat = $this->_module->getSaveFormat($this->type);
        } else {
            $this->saveFormat = Module::parseFormat($this->saveFormat, $this->type);
        }
        if (empty($this->displayTimezone)) {
            $this->displayTimezone = $this->_module->getDisplayTimezone();
        }
        if (empty($this->saveTimezone)) {
            $this->saveTimezone = $this->_module->getSaveTimezone();
        }
        // skip timezone validations when using date only inputs
        if ($this->type === self::FORMAT_DATE) {
            $this->displayTimezone = $this->saveTimezone = null;
        }
        if ($this->autoWidget) {
            $this->_widgetSettings = [
                self::FORMAT_DATE => ['class' => '\kartik\date\DatePicker'],
                self::FORMAT_DATETIME => ['class' => '\kartik\datetime\DateTimePicker'],
                self::FORMAT_TIME => ['class' => '\kartik\time\TimePicker'],
            ];
            Config::validateInputWidget(
                $this->_widgetSettings[$this->type]['class'],
                "for DateControl '{$this->type}' format"
            );
            foreach ($this->_widgetSettings as $type => $setting) {
                $this->_widgetSettings[$type]['options'] = $this->_module->autoWidgetSettings[$type];
                $this->_widgetSettings[$type]['disabled'] = $this->disabled;
                $this->_widgetSettings[$type]['readonly'] = $this->readonly;
            }
        }
        if (empty($this->widgetClass) && !empty($this->_widgetSettings[$this->type]['class'])) {
            $this->widgetClass = $this->_widgetSettings[$this->type]['class'];
        }
    }

    /**
     * Whether a widget is used to render the display.
     *
     * @return boolean
     */
    protected function isWidget()
    {
        return ($this->autoWidget || !empty($this->widgetClass));
    }

    /**
     * Generates the display input.
     *
     * @return string
     * @throws Exception
     */
    protected function getDisplayInput()
    {
        $name = ($this->hasModel() ? $this->attribute : $this->name) . '-' . $this->options['id'];
        $value = !isset($this->value) || $this->value === '' ? '' : $this->getDisplayValue($this->value);
        if (!$this->isWidget()) {
            if (empty($this->options['class'])) {
                $this->options['class'] = 'form-control';
            }
            return Html::textInput($name, $value, $this->options);
        }
        $opts = $this->widgetOptions;
        if (!empty($this->displayFormat) && $this->autoWidget) {
            $defaultOptions = Module::defaultWidgetOptions($this->type, $this->displayFormat);
            $opts = ArrayHelper::merge($defaultOptions, $opts);
        }
        if (!empty($this->_widgetSettings[$this->type]['options'])) {
            $opts = ArrayHelper::merge(
                $this->_widgetSettings[$this->type]['options'], $opts
            );
        }
        unset($opts['model'], $opts['attribute']);
        $opts['name'] = $name;
        $opts['value'] = $value;
        /**
         * @var InputWidget $class
         */
        $class = $this->widgetClass;
        if (property_exists($class, 'disabled')) {
            $opts['disabled'] = $this->disabled;
        }
        if (property_exists($class, 'readonly')) {
            $opts['readonly'] = $this->readonly;
        }
        $this->widgetOptions = $opts;
        return $class::widget($opts);
    }

    /**
     * Generates the save input.
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
     * Translate the date string.
     *
     * @param string $data the input date data.
     * @param string $format the input date format.
     *
     * @return string the translated date.
     */
    protected function translateDate($data, $format)
    {
        $out = $data;
        foreach (self::$_enSettings as $key => $value) {
            if (static::checkFormatKey($format, $key)) {
                $out = $this->translate($out, $key);
            }
        }
        return $out;
    }

    /**
     * Translate a date pattern based on type.
     *
     * @param string $string input date string.
     * @param string $type the type of date pattern as set in [[$_enSettings]].
     *
     * @return string the translated string.
     */
    protected function translate($string, $type)
    {
        if (empty($this->pluginOptions['dateSettings'][$type])) {
            return $string;
        }
        return str_ireplace(self::$_enSettings[$type], $this->pluginOptions['dateSettings'][$type], $string);
    }

    /**
     * Sets the locale using the locales configuration settings.
     */
    protected function setLocale()
    {
        if (!$this->_doTranslate || !empty($this->pluginOptions['dateSettings'])) {
            return;
        }
        $file = static::getLocaleFile($this->language);
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            $this->pluginOptions['dateSettings'] = require($file);
        }
    }

    /**
     * Registers assets for the [[DateControl]] widget.
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DateFormatterAsset::register($view);
        DateControlAsset::register($view);
        $pluginOptions = empty($this->pluginOptions) ? [] : $this->pluginOptions;
        $this->pluginOptions = ArrayHelper::merge(
            [
                'idSave' => $this->saveOptions['id'],
                'url' => $this->ajaxConversion ? Url::to($this->_module->convertAction) : '',
                'type' => $this->type,
                'saveFormat' => $this->saveFormat,
                'dispFormat' => $this->displayFormat,
                'saveTimezone' => $this->saveTimezone,
                'dispTimezone' => $this->displayTimezone,
                'asyncRequest' => $this->asyncRequest,
            ], $pluginOptions
        );
        $this->registerPlugin($this->pluginName);
        $pluginData = 'data-krajee-' . $this->pluginName;
        if (!empty($this->options[$pluginData])) {
            $this->widgetOptions['options'][$pluginData] = $this->options[$pluginData];
        }
    }
}