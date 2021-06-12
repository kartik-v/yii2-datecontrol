Change Log: `yii2-datecontrol`
==============================

## version 1.9.8

**Date:** 12-Jun-2021

- (enh #133): Correct conversion for FORMAT_DATE.

## version 1.9.7

**Date:** 23-Sep-2018

- Move all source code to the `src` directory.
- Enhancements to support Bootstrap v4.x.
- (enh #124): Load settings from request in ParseController.php.
- (bug #121): Add delay before run validate(), while "timepicker" make changes.
- (enh #120): Better post params validation.
- (enh #113): Correct empty value validation.

## version 1.9.6

**Date:** 14-Jan-2017

- Better configuration for `Module::convertAction`.
- (bug #108): Initialize auto widget options correctly.
- (bug #106): More correct validation for guessing date without ajax conversion.
- (bug #105): Set date control plugin data correctly within widget options.
- (enh #96, #102): Ignore timezone conversion for `DateControl::FORMAT_DATE`.
- (enh #92): Implement following DateControl plugin events that can be listened via jQuery/javascript:
   - `beforechange.datecontrol`: will be triggered before an ajax request is sent when changing the date input on client.
   - `changesuccess.datecontrol`: will be triggered after successful change of a date on the client(applicable for both ajax or non ajax conversions). 
   - `changeerror.datecontrol`: will be triggered when the ajax conversion service returns an error status.
   - `changecomplete.datecontrol`: will be triggered after completion of an ajax conversion service.
   - `changeajaxerror.datecontrol`: will be triggered when any exception or error is thrown during the ajax conversion.
   - `afterpaste.datecontrol`: will be triggered when a data is pasted in the date input on the client.   

## version 1.9.5

**Date:** 08-Dec-2016

- Add github contribution and issue/PR logging templates.
- Enhance PHP Documentation for all classes and methods in the extension.
- Add branch alias for dev-master latest release.
- (enh #94): Add paste support for DateControl.
- (bug #103): **BC BREAKING CHANGE**: A new property `widgetOptions` is available. This property will replace the `options` property for the scenario when `autoWidget` or `widgetClass` is set.

## version 1.9.4

**Date:** 30-Jul-2015

- (enh #69): Better format conversion to reset unprovided fields to the Unix Epoch.
- (bug #68): Better `strpos` validation before ajax conversion.
- (enh #67): Allow multiple locale widgets to be parsed correctly on same page.

## version 1.9.3

**Date:** 19-Jul-2015

- (enh #66): Localisation format parsing enhancements.
- (enh #59): Enhancements for parsing `yii2-widget-datepicker` better.
- Undo #50 - remove timestamp increase through up/down keys.

## version 1.9.2

**Date:** 02-Jun-2015

- (enh #58, #60): Fix for triggering asynchronous change event.
- (bug #51): Typo in plugin validation for type.
- (bug #50): Pressing up/down keys to increase/decrease timestamp.

## version 1.9.1

**Date:** 13-Feb-2015

- Set copyright year to current.
- (bug #43): Allow `datecontrol` module to be used as an embedded submodule.
- (bug #42): Prevent double ajax requests due to plugin internal change events getting triggered.
- (bug #39): Ensure datecontrol is validated on blur.
- (enh #37): Wrong capitalization in 'autoClose'.
- (bug #36): Update default save format settings to php: syntax.

## version 1.9.0

**Date:** 13-Dec-2014

- (bug #35): Auto convert display and save formats correctly to PHP DateTime format.
- (bug #34): Bug in Module Methods "getDisplayFormat" and "getSaveFormat" converted a correct php format in an incorrect one.
- (bug #34): Locals with short language code like "de" haven't been found because "prefix" was not in string. 

## version 1.8.0

**Date:** 04-Dec-2014

- (enh #33): Auto validate disability using new `disabled` and `readonly` properties in InputWidget
- (enh #31): Enhance widget to use updated plugin registration from Krajee base 

## version 1.7.0

**Date:** 17-Nov-2014

- (enh #28, #29): DateTime createFromFormat wrongly uses current timestamp in time part for bare DATE format.
- (enh #27): Added property for switching between asynchronous or synchronous request via Ajax.

## version 1.6.0

**Date:** 10-Nov-2014

- Set dependency on Krajee base component.

## version 1.5.0

**Date:** 10-Oct-2014

- (enh #22): Extension revamped to support PHP and ICU date formats 

## version 1.4.0

**Date:** 08-Oct-2014

- (enh #21): Enhance date format con## version based on new yii helper `FormatConverter` (enrica).

## version 1.3.0

**Date:** 24-Jul-2014

- (enh #18): Included timezone support for display and save formats (requires `ajaxConversion`).
- PSR 4 alias change

## version 1.2.0

**Date:** 24-Jul-2014

- (enh #14, #15): Revamped and enhanced datecontrol plugin to work with the [php-date-formatter.js](https://github.com/kartik-v/php-date-formatter) jQuery plugin.
- The extension now has an option to either use `ajaxConversion` OR use client level javascript validation to convert date. Ajax con## version is disabled by default.
- Change and Keydown events revamped. The extension now automatically listens to the UP and DOWN presses for the DatePicker widget.
- Preconfigured locales matching DatePicker. Includes a locales folder for date settings configuration for each language.
- Ability to override locale date settings at runtime for each DateControl widget instance.

## version 1.1.0

**Date:** 26-Jun-2014

- (enh #12): Updated documentation for new `autoWidgetSettings` as per enh # 9.
- (enh #11): Use date con## version using PHP DateTime instead of Yii formatter
- (bug #10): Fix DatePicker convertFormat to work with DateControl.
- (enh #9): Included `autoWidgetSettings` in module, for configuring global settings for `kartik\widgets` when `autoWidget` is true.
- (enh #9): Defaulting rules vastly enhanced. Included the configurable properties `dateControlDisplay` and `dateControlSave` in 
   `Yii::$app->params`, which can override the module level `displaySettings` and `saveSettings`.
- (enh #4): Fix documentation to include right namespace for Module.
- (enh #4): Fix documentation to include right namespace for Module.
- (bug #3): Fix AutoWidget Plugin Options using right array merge.

## version 1.0.0

**Date:** 01-Jun-2014
Initial release
