/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 1.7.0
 *
 * Date control validation plugin
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function ($) {
    var isEmpty = function(value, trim) {
        return value === null || value === undefined || value == []
            || value === '' || trim && $.trim(value) === '';
    };

    var DateControl = function (element, options) {
        this.$element = $(element);
        this.init(options);
        this.listen();
    };

    DateControl.prototype = {
        constructor: DateControl,
        init: function (options) {
            var self = this,
                vSettings = isEmpty(options.dateSettings) ? {} : {dateSettings: options.dateSettings};
            self.$idSave = $("#" + options.idSave);
            self.url = options.url;
            self.reqType = options.type;
            self.dispFormat = options.dispFormat;
            self.saveFormat = options.saveFormat;
            self.dispTimezone = options.dispTimezone;
            self.saveTimezone = options.saveTimezone;
            self.asyncRequest = options.asyncRequest;
            self.dateFormatter = new DateFormatter(vSettings);
        },
        listen: function () {
            var self = this, $el = self.$element, $idSave = self.$idSave, vUrl = self.url,
                vType = self.reqType, vDispFormat = self.dispFormat, vSaveFormat = self.saveFormat,
                vDispTimezone = self.dispTimezone, vSaveTimezone = self.saveTimezone, 
                vAsyncRequest = self.asyncRequest, vFormatter = self.dateFormatter;
            $el.on('change', function () {
                if (isEmpty($el.val())) {
                    $idSave.val('');
                } else {
                    if (isEmpty(vUrl)) {
                        var vDispDate = vFormatter.parseDate($el.val(), vDispFormat);
                        if (vDispDate == false) {
                            vDispDate = vFormatter.guessDate($el.val(), vDispFormat);
                            $el.val(vFormatter.formatDate(vDispDate, vDispFormat));
                        }
                        $idSave.val(vFormatter.formatDate(vDispDate, vSaveFormat));
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
                                if (data.status == "success") {
                                    $idSave.val(data.output);
                                }
                            }
                        });
                    }
                }
                $idSave.trigger('change');
            });
            $el.on('keydown', function (e) {
                if (isEmpty($el.val())) {
                    $el.val('');
                } else {
                    switch (e.keyCode) {
                        case 27:    // Esc key
                            if ($.isFunction($el.parent().datepicker)) {
                                $el.parent().datepicker("hide");
                                e.preventDefault;
                            }
                            if ($.isFunction($el.datepicker)) {
                                $el.datepicker("hide");
                                e.preventDefault;
                            }
                            break;
                        case 38:    // Up arrow
                            e.preventDefault();
                            var vDate = vFormatter.parseDate($el.val(), vDispFormat);
                            if (vDate != false) {
                                vDate.setDate(vDate.getDate() + 1);
                            }
                            break;
                        case 40:    // Down arrow
                            e.preventDefault();
                            var vDate = vFormatter.parseDate($el.val(), vDispFormat);
                            if (vDate != false) {
                                vDate.setDate(vDate.getDate() - 1);
                            }
                            break;

                    }
                    if (e.keyCode == 38 || e.keyCode == 40) {
                        vDate = vFormatter.formatDate(vDate, vDispFormat);
                        $el.val(vDate);
                        $el.trigger("change");
                        if ($.isFunction($el.parent().datepicker)) {
                            $el.parent().datepicker('update');
                        }
                        if ($.isFunction($el.datepicker)) {
                            $el.datepicker('update');
                        }
                    }
                }
                $idSave.trigger('keydown');
            });
        }
    };

    // DateControl plugin definition
    $.fn.datecontrol = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this),
                data = $this.data('datecontrol'),
                options = typeof option === 'object' && option;

            if (!data) {
                $this.data('datecontrol', (data = new DateControl(this, $.extend({}, $.fn.datecontrol.defaults, options, $(this).data()))));
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
            longMonths: ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'],
            meridiem: ['AM', 'PM']
        },
        dispTimezone: null,
        saveTimezone: null
    };

}(jQuery));