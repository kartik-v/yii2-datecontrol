yii2-datecontrol
================

The **Date Control** module allows controlling date formats of attributes separately for View and Model for Yii Framework 2.0.

## Why Date Control?

When working with the great Yii Framework, one of the most common observations I had was the need to have a proper control on the date settings. The date settings for each 
Yii application, are unique to each application and region. Most Yii developers or users almost always need an option of displaying date and time in ONE specific format, 
but save them to database in ANOTHER format. So to summarize, the problem statement was:

- Lack of a single configuration method to display date & times to user (or VIEW) in ONE format
- Lack of a configuration method to save date & times in database (or MODEL) in ANOTHER format

Most existing Yii solutions try to overcome the above by setting the format in `model->afterFind`, present in view, then unformat it in `model->setAttribues` or `model->beforeValidate`.
This was still an issue when one had applications with many models and views and changes had to be replicated across (more complex scenarios being multi-regional formats).

This module helps overcome this large gap by addressing all of these at the presentational level. The module enables one to configure the date and time 
settings separately for DISPLAY and SAVE. This can be setup either globally or individually at each DateControl widget level. And if this is not useful enough, it 
automatically enables any date/time picker widgets to be used in conjunction with this.

How this magic works, is that the extension just alters this at the presentational layer (VIEW). It automatically sets the base model input to hidden and displays
a mirror input in the display format one has set. Then on each edit of the display input, the extension traps the change event, and overrwrites the hidden base model 
input as per the desired save format. The other good thing is, that the extension automatically triggers the javascript change event for the base model input 
as well. Thus all client model validations and other jquery events needed by Picker widgets are automatically triggered.

> NOTE: All date and time formats used across this module follow one standard - i.e. [PHP Date Time format strings](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters). The extension automatically
provides three widgets to display and control the date-time inputs. 

- [\kartik\widgets\DatePicker](http://demos.krajee.com/widget-details/DatePicker) if your format type is `date`
- [\kartik\widgets\TimePicker](http://demos.krajee.com/widget-details/TimePicker) if your format type is `time`
- [\kartik\widgets\DateTimePicker](http://demos.krajee.com/widget-details/DateTimePicker) if your format type is `datetime`


## Module

The extension has been created as a module to enable access to global settings for your application. In addition, it allows you to read and format date times
between client and server using PHP DateTime object. The DateControl widget uses ajax processing to convert display (view) format to model (save) format.

```php
'modules' => [
   'datecontrol' =>  [
        'class' => 'kartik\datecontrol\Module',
        
        // format settings for displaying each date attribute
        'displaySettings' => [
            Module::FORMAT_DATE => 'd-M-Y',
            Module::FORMAT_TIME => 'H:i:s A',
            Module::FORMAT_DATETIME => 'd-M-Y H:i:s A',
        ],
        
        // format settings for saving each date attribute
        'saveSettings' => [
            Module::FORMAT_DATE => 'U', // saves as unix timestamp
            Module::FORMAT_TIME => 'H:i:s',
            Module::FORMAT_DATETIME => 'Y-m-d H:i:s',
        ]
        
        // automatically use kartik\widgets for each of the above formats
        'autoWidget' => true,
        
        // custom widget settings that will be used to render the date input instead of kartik\widgets,
        // this will be used when autoWidget is set to false at module or widget level.
        'widgetSettings' => [
            Module::FORMAT_DATE => [
                'class' => 'yii\jui\DatePicker', 
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

## DateControl

The main widget for rendering each date control on your views. Many settings will be defaulted from the module setup, but can be overridden
at the widget level. An usage example with `ActiveForm` and using `\kartik\widgets\DateTimePicker` is shown below.

```php
echo $form->field($model, 'datetime_2')->widget(DateControl::classname(), [
    'displayFormat' => 'd-M-Y H:i:s',
    'type'=>DateControl::FORMAT_DATETIME
]);
```

### Demo
You can see detailed [documentation](http://demos.krajee.com/datecontrol) and [demonstration](http://demos.krajee.com/datecontrol-demo) on usage of the extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

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
