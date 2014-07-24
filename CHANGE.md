version 1.1.0
=============
**Date:** 2014-06-26

1. (bug #3): Fix AutoWidget Plugin Options using right array merge.
2. (enh #4): Fix documentation to include right namespace for Module.
3. (enh #4): Fix documentation to include right namespace for Module.
4. (enh #9): Included `autoWidgetSettings` in module, for configuring global settings for `kartik\widgets` when `autoWidget` is true.
5. (enh #9): Defaulting rules vastly enhanced. Included the configurable properties `dateControlDisplay` and `dateControlSave` in 
   `Yii::$app->params`, which can override the module level `displaySettings` and `saveSettings`.
6. (bug #10): Fix DatePicker convertFormat to work with DateControl.
7. (enh #11): Use date conversion using PHP DateTime instead of Yii formatter
8. (enh #12): Updated documentation for new `autoWidgetSettings` as per enh # 9.

version 1.0.0
=============
**Date:** 2014-06-01
Initial release
