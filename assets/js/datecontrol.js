/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015
 * @version 1.9.1
 *
 * Date control validation plugin
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function ($) {
    "use strict";
    var isEmpty = function (value, trim) {
            return value === null || value === undefined || value.length === 0 || (trim && $.trim(value) === '');
        },
        DateControl = function (element, options) {
            this.$element = $(element);
            this.init(options);
            this.listen();
        };

    DateControl.prototype = {
        constructor: DateControl,
        init: function (options) {
            var self = this,
                vSettings = isEmpty(options.dateSettings) ? {} : {dateSettings: options.dateSettings};
            $.each(options, function (key, value) {
                self[key] = value;
            });
            self.$idSave = $("#" + options.idSave);
            self.dateFormatter = window.DateFormatter ? new window.DateFormatter(vSettings) : {};
            self.isChanged = false;
        },
        validate: function () {
            var self = this, $el = self.$element, $idSave = self.$idSave, vUrl = self.url,
                vType = self.type, vDispFormat = self.dispFormat, vSaveFormat = self.saveFormat,
                vDispTimezone = self.dispTimezone, vSaveTimezone = self.saveTimezone,
                vAsyncRequest = self.asyncRequest, vFormatter = self.dateFormatter;
            if (self.isChanged) {
                return;
            }
            self.isChanged = true;
            if (isEmpty($el.val())) {
                $idSave.val('');
                self.isChanged = false;
            } else {
                if (isEmpty(vUrl)) {
                    var vDispDate = vFormatter.parseDate($el.val(), vDispFormat);
                    if (vDispDate === false) {
                        vDispDate = vFormatter.guessDate($el.val(), vDispFormat);
                        $el.val(vFormatter.formatDate(vDispDate, vDispFormat));
                    }
                    $idSave.val(vFormatter.formatDate(vDispDate, vSaveFormat));
                    self.isChanged = false;
                } else {
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
                            saveTimezone: vSaveTimezone
                        },
                        success: function (data) {
                            if (data.status === "success") {
                                $idSave.val(data.output);
                                $idSave.trigger('change');
                            }
                        },
                        complete: function () {
                            self.isChanged = false;
                        },
                        error: function () {
                            self.isChanged = false;
                        }
                    });
                }
            }
        },
        listen: function () {
            var self = this, $el = self.$element, $idSave = self.$idSave,
                vDispFormat = self.dispFormat, vFormatter = self.dateFormatter;
            $el.on('change', function () {
                self.validate();
            }).on('keydown', function (e) {
                var vDate, val, typ;
                if (isEmpty($el.val()) || isEmpty(vFormatter)) {
                    return;
                }
                if (e.keyCode === 38 || e.keyCode === 40) { // Up or Down key
                    vDate = vFormatter.parseDate($el.val(), vDispFormat);
                    e.preventDefault();
                    if (!vDate) {
                        return;
                    }
                    val = vDate.getDate() + 39 - (+e.keyCode);
                    vDate.setDate(val);
                    val = vFormatter.formatDate(vDate, vDispFormat);
                    $el.val(val).trigger("change");
                    typ = $el.attr('data-datepicker-type');
                    if (!isEmpty(typ)) {
                        switch (typ) {
                            case 1:
                                $el.datepicker('update');
                                break;
                            case 5:
                                $el.parent().parent().datepicker('update');
                                break;
                            default:
                                $el.parent().datepicker('update');
                        }
                    }
                }
                $idSave.trigger('keydown');
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
        dateSettings: {
            longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            longMonths: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            meridiem: ['AM', 'PM']
        },
        dispTimezone: null,
        saveTimezone: null,
        asyncRequest: true
    };

    $.fn.datecontrol.Constructor = DateControl;
}(window.jQuery));