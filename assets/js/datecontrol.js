/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version 1.9.6
 *
 * Date control validation plugin
 * 
 * Author: Kartik Visweswaran
 *
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    "use strict";
    var NAMESPACE = '.datecontrol',
        isEmpty = function (value, trim) {
            return value === null || value === undefined || value.length === 0 || (trim && $.trim(value) === '');
        },
        DateControl = function (element, options) {
            var self = this;
            self.$element = $(element);
            self.init(options);
            self.listen();
        };

    DateControl.prototype = {
        constructor: DateControl,
        init: function (options) {
            var self = this,
                vSettings = isEmpty(options.dateSettings) ? {} : {dateSettings: options.dateSettings};
            $.each(options, function (key, value) {
                self[key] = value;
            });
            /** @namespace options.idSave */
            self.$idSave = $("#" + options.idSave);
            self.dateFormatter = window.DateFormatter ? new window.DateFormatter(vSettings) : {};
            if (isEmpty(self.dateFormatter)) {
                throw "No DateFormatter plugin found. Ensure you have 'php-date-formatter.js' loaded.";
            }
            self.isChanged = false;
        },
        raise: function (event, params) {
            var self = this, e = $.Event(event + NAMESPACE), $el = self.$element;
            if (params !== undefined) {
                $el.trigger(e, params);
            } else {
                $el.trigger(e);
            }
        },
        validate: function () {
            var self = this, $el = self.$element, $idSave = self.$idSave, vUrl = self.url,
                vType = self.type, vDispFormat = self.dispFormat, vSaveFormat = self.saveFormat,
                vDispTimezone = self.dispTimezone, vSaveTimezone = self.saveTimezone,
                vAsyncRequest = self.asyncRequest, vFormatter = self.dateFormatter, vSettings;
            if (self.isChanged) {
                return;
            }
            self.isChanged = true;
            if (isEmpty($el.val())) {
                $idSave.val('').trigger('change');
                self.raise('changesuccess', [$el.val(), $idSave.val()]);
                self.isChanged = false;
            } else {
                if (isEmpty(vUrl)) {
                    var vDispDate = vFormatter.parseDate($el.val(), vDispFormat);
                    if (vDispDate === false || vDispDate === null || String(vDispDate).length === 0) {
                        vDispDate = vFormatter.guessDate($el.val(), vDispFormat);
                        $el.val(vFormatter.formatDate(vDispDate, vDispFormat));
                    }
                    $idSave.val(vFormatter.formatDate(vDispDate, vSaveFormat)).trigger('change');
                    self.raise('changesuccess', [$el.val(), $idSave.val()]);
                    self.isChanged = false;
                } else {
                    vSettings = self.language.substring(0, 2) === 'en' ? [] : self.dateSettings;
                    $.ajax({
                        url: vUrl,
                        type: "post",
                        dataType: "json",
                        async: vAsyncRequest,
                        data: {
                            displayDate: $el.val(),
                            type: vType,
                            dispFormat: vDispFormat,
                            saveFormat: vSaveFormat,
                            dispTimezone: vDispTimezone,
                            saveTimezone: vSaveTimezone,
                            settings: vSettings
                        },
                        beforeSend: function (jqXHR) {
                            self.raise('beforechange', [$el.val(), $idSave.val(), jqXHR]);
                        },
                        success: function (data, textStatus, jqXHR) {
                            var ev = 'changeerror';
                            if (data.status === "success") {
                                $idSave.val(data.output).trigger('change');
                                ev = 'changesuccess';
                            }
                            self.raise(ev, [$el.val(), $idSave.val(), data, textStatus, jqXHR]);
                        },
                        complete: function () {
                            self.isChanged = false;
                            self.raise('changecomplete', [$el.val(), $idSave.val()]);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            self.isChanged = false;
                            self.raise('changeajaxerror', [$el.val(), $idSave.val(), jqXHR, textStatus, errorThrown]);
                        }
                    });
                }
            }
        },
        listen: function () {
            var self = this, $el = self.$element;
            $el.on('change', function () {
                self.validate();
            }).on('paste', function () {
                setTimeout(function () {
                    $el.val($el.val());
                    self.validate();
                    self.raise('afterpaste');
                }, 100);
            });
        }
    };

    $.fn.datecontrol = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this),
                data = $this.data('datecontrol'),
                options = typeof option === 'object' && option;
            if (!data) {
                data = new DateControl(this, $.extend({}, $.fn.datecontrol.defaults, options, $(this).data()));
                $this.data('datecontrol', data);
            }
            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.datecontrol.defaults = {
        language: 'en',
        dateSettings: {
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            months: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            meridiem: ['AM', 'PM']
        },
        dispTimezone: null,
        saveTimezone: null,
        asyncRequest: true
    };

    $.fn.datecontrol.Constructor = DateControl;
}(window.jQuery));