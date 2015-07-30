Change Log: `yii2-datecontrol`
==============================

## version 1.9.4

**Date:** 30-Jul-2015

- (enh #67): Allow multiple locale widgets to be parsed correctly on same page.
- (bug #68): Better `strpos` validation before ajax conversion.
- (enh #69): Better format conversion to reset unprovided fields to the Unix Epoch.

## version 1.9.3

**Date:** 19-Jul-2015

- (enh #59): Enhancements for parsing `yii2-widget-datepicker` better.
- (enh #66): Localisation format parsing enhancements.
- Undo #50 - remove timestamp increase through up/down keys.

## version 1.9.2

**Date:** 02-Jun-2015

- (bug #50): Pressing up/down keys to increase/decrease timestamp.
- (bug #51): Typo in plugin validation for type.
- (enh #58, #60): Fix for triggering asynchronous change event.

## version 1.9.1

**Date:** 13-Feb-2015

- (bug #36): Update default save format settings to php: syntax.
- (enh #37): Wrong capitalization in 'autoClose'.
- (bug #39): Ensure datecontrol is validated on blur.
- (bug #42): Prevent double ajax requests due to plugin internal change events getting triggered.
- (bug #43): Allow `datecontrol` module to be used as an embedded submodule.
- Set copyright year to current.

## version 1.9.0

**Date:** 13-Dec-2014

- (bug #34): Locals with short language code like "de" haven't been found because "prefix" was not in string. 
- (bug #34): Bug in Module Methods "getDisplayFormat" and "getSaveFormat" converted a correct php format in an incorrect one.
- (bug #35): Auto convert display and save formats correctly to PHP DateTime format.

## version 1.8.0

**Date:** 04-Dec-2014

- (enh #31): Enhance widget to use updated plugin registration from Krajee base 
- (enh #33): Auto validate disability using new `disabled` and `readonly` properties in InputWidget

## version 1.7.0

**Date:** 17-Nov-2014

- enh #27: Added property for switching between asynchronous or synchronous request via Ajax.
- enh #28, #29: DateTime createFromFormat wrongly uses current timestamp in time part for bare DATE format.
- Set release to stable.

## version 1.6.0

**Date:** 10-Nov-2014

- Set dependency on Krajee base component.

## version 1.5.0

**Date:** 10-Oct-2014

1. enh #22: Extension revamped to support PHP and ICU date formats 

## version 1.4.0

**Date:** 08-Oct-2014

1. enh #21: Enhance date format con## version based on new yii helper `FormatConverter` (enrica).

## version 1.3.0

**Date:** 24-Jul-2014

1. enh #18: Included timezone support for display and save formats (requires `ajaxConversion`).
2. PSR 4 alias change

## version 1.2.0

**Date:** 24-Jul-2014

1. (enh #14, #15): Revamped and enhanced datecontrol plugin to work with the [php-date-formatter.js](https://github.com/kartik-v/php-date-formatter) jQuery plugin.
2. The extension now has an option to either use `ajaxConversion` OR use client level javascript validation to convert date. Ajax con## version is disabled by default.
3. Change and Keydown events revamped. The extension now automatically listens to the UP and DOWN presses for the DatePicker widget.
4. Preconfigured locales matching DatePicker. Includes a locales folder for date settings configuration for each language.
5. Ability to override locale date settings at runtime for each DateControl widget instance.

## version 1.1.0

**Date:** 26-Jun-2014

1. (bug #3): Fix AutoWidget Plugin Options using right array merge.
2. (enh #4): Fix documentation to include right namespace for Module.
3. (enh #4): Fix documentation to include right namespace for Module.
4. (enh #9): Included `autoWidgetSettings` in module, for configuring global settings for `kartik\widgets` when `autoWidget` is true.
5. (enh #9): Defaulting rules vastly enhanced. Included the configurable properties `dateControlDisplay` and `dateControlSave` in 
   `Yii::$app->params`, which can override the module level `displaySettings` and `saveSettings`.
6. (bug #10): Fix DatePicker convertFormat to work with DateControl.
7. (enh #11): Use date con## version using PHP DateTime instead of Yii formatter
8. (enh #12): Updated documentation for new `autoWidgetSettings` as per enh # 9.

## version 1.0.0

**Date:** 01-Jun-2014
Initial release
