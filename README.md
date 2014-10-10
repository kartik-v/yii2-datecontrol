yii2-datecontrol
================

The **Date Control** module allows controlling date formats of attributes separately for View and Model for Yii Framework 2.0.

> NOTE: This extension depends on the [kartik-v/yii2-widgets](https://github.com/kartik-v/yii2-widgets) extension which in turn depends on the
[yiisoft/yii2-bootstrap](https://github.com/yiisoft/yii2/tree/master/extensions/bootstrap) extension. Check the 
[composer.json](https://github.com/kartik-v/yii2-datecontrol/blob/master/composer.json) for this extension's requirements and dependencies. 
Note: Yii 2 framework is still in active development, and until a fully stable Yii2 release, your core yii2-bootstrap packages (and its dependencies) 
may be updated when you install or update this extension. You may need to lock your composer package versions for your specific app, and test 
for extension break if you do not wish to auto update dependencies.

With version v1.2.0 this extension now depends on the new Krajee jQuery library [php-date-formatter](http://plugins.krajee.com/php-date-formatter).
The extension can thus now easily read date & time stamps consistently in ONE format (PHP DateTime) across the client and server. However, it is 
recommended to use `ajaxConversion` if you need seamless integration with PHP DateTime functions like timezone support.

> NOTE: Version 1.5.0 has BC breaking changes. It supports both ICU and PHP date format patterns. In order to pass a PHP Date format - prepend your 
format pattern with the string `php:`. 

## Why Date Control?

> Version 1.5.0 has been released. Refer [CHANGE LOG](https://github.com/kartik-v/yii2-datecontrol/blob/master/CHANGE.md) for details.

When working with the great Yii Framework, one of the most common observations I had was the need to have a proper control on the date settings. The date settings for each 
Yii application, are unique to each application and region. Most Yii developers or users almost always need an option of displaying date and time in ONE specific format, 
but save them to database in ANOTHER format. So to summarize, the problem statement was:

- Lack of a single configuration method to display date & times to user (or VIEW) in ONE format
- Lack of a configuration method to save date & times in database (or MODEL) in ANOTHER format

Most existing Yii solutions try to overcome the above by setting the format in `model->afterFind`, present in view, then unformat it in `model->setAttribues` or `model->beforeValidate`.
This was still an issue when one had many models and views in the application and changes in development cycle, had to be replicated in many places (more complex scenarios being multi-regional formats).

This module helps overcome this large gap by addressing all of these at the presentational level. The module enables one to configure the date and time 
settings separately for DISPLAY and SAVE. This can be setup either globally or individually at each DateControl widget level. And if this is not useful enough, it 
automatically enables any date/time picker widgets to be used in conjunction with this.

How this magic works, is that the extension just alters this at the presentational layer (VIEW). It automatically sets the base model input to hidden and displays
a mirror input in the display format one has set. Then on each edit of the display input, the extension traps the change event, and overrwrites the hidden base model 
input as per the desired save format. The other good thing is, that the extension automatically triggers the javascript change event for the base model input 
as well. Thus all client model validations and other jquery events needed by Picker widgets are automatically triggered.

> NOTE: All date and time formats used across this module follow one standard - i.e. [PHP Date Time format strings](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters). The extension automatically
provides three widgets to display and control the date-time inputs. 

- [\kartik\widgets\DatePicker](http://demos.krajee.com/widget-details/datepicker) if your format type is `date`
- [\kartik\widgets\TimePicker](http://demos.krajee.com/widget-details/timepicker) if your format type is `time`
- [\kartik\widgets\DateTimePicker](http://demos.krajee.com/widget-details/datetimepicker) if your format type is `datetime`


## Module

The extension has been created as a module to enable access to global settings for your application. In addition, it allows you to read and format date times
between client and server using PHP DateTime object. The DateControl widget uses ajax processing to convert display (view) format to model (save) format.

```php
use kartik\datecontrol\Module;
'modules' => [
   'datecontrol' =>  [
        'class' => 'kartik\datecontrol\Module',
        
        // format settings for displaying each date attribute (ICU format example)
        'displaySettings' => [
            Module::FORMAT_DATE => 'dd-MM-yyyy',
            Module::FORMAT_TIME => 'HH:mm:ss a',
            Module::FORMAT_DATETIME => 'dd-MM-yyyy HH:mm:ss A', 
        ],
        
        // format settings for saving each date attribute (PHP format example)
        'saveSettings' => [
            Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
            Module::FORMAT_TIME => 'php:H:i:s',
            Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
        ],
        
        // automatically use kartik\widgets for each of the above formats
        'autoWidget' => true,
        
        // use ajax conversion for processing dates from display format to save format.
        'ajaxConversion' => false,

        // default settings for each widget from kartik\widgets used when autoWidget is true
        'autoWidgetSettings' => [
            Module::FORMAT_DATE => ['type'=>2, 'pluginOptions'=>['autoclose'=>true]], // example
            Module::FORMAT_DATETIME => [], // setup if needed
            Module::FORMAT_TIME => [], // setup if needed
        ],
        
        // custom widget settings that will be used to render the date input instead of kartik\widgets,
        // this will be used when autoWidget is set to false at module or widget level.
        'widgetSettings' => [
            Module::FORMAT_DATE => [
                'class' => 'yii\jui\DatePicker', // example
                'options' => [
                    'options'=>['class'=>'form-control'],
                    'clientOptions' => ['dateFormat' => 'dd-mm-yy'],
                ]
            ]
        ]
        // other settings
    ]
];
```

## Params Configuration

The extension allows configuration of `dateControlDisplay` and `dateControlSave` settings at Yii application params level. The params configuration will override the settings at the module level. 
This configuration is useful when one wants to dynamically change these params settings at runtime. The settings can be overridden at DateControl widget level.

Refer the [defaulting rules documentation](http://demos.krajee.com/datecontrol#defaults) for details.

## DateControl

The main widget for rendering each date control on your views. Many settings will be defaulted from the module setup, but can be overridden
at the widget level. An usage example with `ActiveForm` and using `\kartik\widgets\DateTimePicker` is shown below. Note you can pass date-time
formats as supported by ICU or PHP. To set a PHP date time format - prepend the format string with `php:` as shown below.

```php
echo $form->field($model, 'datetime_2')->widget(DateControl::classname(), [
    'displayFormat' => 'php:d-M-Y H:i:s',
    'type'=>DateControl::FORMAT_DATETIME
]);
```

### Demo
You can see detailed [documentation and usage](http://demos.krajee.com/datecontrol) and a [demonstration](http://demos.krajee.com/datecontrol-demo) on the extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

> Note: You must set the `minimum-stability` to `dev` in the **composer.json** file in your application root folder before installation of this extension.

Either run

```
$ php composer.phar require kartik-v/yii2-datecontrol "dev-master"
```

or add

```
"kartik-v/yii2-datecontrol": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage
```php
use kartik\datecontrol\Module;
use kartik\datecontrol\DateControl;
```

## License

**yii2-datecontrol** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
