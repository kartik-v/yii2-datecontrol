/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 1.0.0
 *
 * Date control validation script
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2013, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function($){
function isEmpty(value, trim) {
    return value === null || value === undefined || value == []
        || value === '' || trim && $.trim(value) === '';
};

var $dateTerms;

var formatter = {
    separators: /[ -+\/\.T:@]/g,
    validParts: /[djDlwSFmMnyYaAgGhHisU]/g,
    parseDate: function($date, $inpFormat){
        if (!$date)
            return undefined;
        if ($date instanceof Date)
            return $date;
        if (typeof $date == 'number')
            return new Date($date);

        var parsedDatetime = {
            date: null,
            year: null,
            month: null,
            day: null,
            dayOfWeek: null,
            hour: 0,
            min: 0,
            sec: 0
        };
        
        if ($date instanceof Date){
            return $date;
        } else if (typeof $date == 'string'){
            var $formatParts = $inpFormat.match(this.validParts);
            if (!$formatParts || $formatParts.length === 0) {
                throw new Error("Invalid date format definition.");
            }
        
            var $dateParts = $date.replace(this.separators, '\0').split('\0');

            var i, $date_flag = false , $time_flag = false;
            for (i = 0; i < $dateParts.length; i++){
                switch ($formatParts[i]){
                    case 'y':
                    case 'Y':
                        if ($dateParts[i].length == 2){
                            parsedDatetime.year = parseInt(((parseInt($dateParts[i]) < 70) ? '20' : '19') + $dateParts[i]);
                        } else if ($dateParts[i].length == 4){
                            parsedDatetime.year = parseInt($dateParts[i]);
                        }
                        $date_flag = true;
                        break;
                    case 'm':
                    case 'n':
                    case 'M':
                    case 'F':
                        var $month;
                        if (isNaN($dateParts[i])){
                            $month = $dateTerms.shortMonthsInYear.indexOf($dateParts[i]);
                            if ($month > -1) {
                                parsedDatetime.month = $month + 1;
                            }
                            $month = $dateTerms.longMonthsInYear.indexOf($dateParts[i]);
                            if ($month > -1) {
                                parsedDatetime.month = $month + 1;
                            }
                        } else {
                            if (parseInt($dateParts[i]) >= 1 && parseInt($dateParts[i]) <= 12){
                                parsedDatetime.month = parseInt($dateParts[i]);
                            }
                        }
                        $date_flag = true;
                        break;
                    case 'd':
                    case 'j':
                        if (parseInt($dateParts[i]) >= 1 && parseInt($dateParts[i]) <= 31){
                            parsedDatetime.day = parseInt($dateParts[i]);
                        }
                        $date_flag = true;
                        break;
                    case 'g':
                    case 'h':
                        var $meriIndex = ($formatParts.indexOf('a') > -1)? $formatParts.indexOf('a') :
                                ($formatParts.indexOf('A') > -1)? $formatParts.indexOf('A') : -1;
                        
                        if ($meriIndex > -1){
                            var $meriOffSet = ($dateParts[$meriIndex].toLowerCase == $dateTerms.meridium[0].toLowerCase)? 0 :
                                     ($dateParts[$meriIndex].toLowerCase == $dateTerms.meridium[1].toLowerCase)? 12 : -1;
                             
                            if (parseInt($dateParts[i]) >= 1 && parseInt($dateParts[i]) <= 12 && $meriOffSet > -1){
                                parsedDatetime.hour = parseInt($dateParts[i]) + $meriOffSet - 1;
                            } else if (parseInt($dateParts[i]) >= 0 && parseInt($dateParts[i]) <= 23){
                                parsedDatetime.hour = parseInt($dateParts[i]);
                            }
                        } else if (parseInt($dateParts[i]) >= 0 && parseInt($dateParts[i]) <= 23){
                            parsedDatetime.hour = parseInt($dateParts[i]);
                        } 
                        $time_flag = true;
                        break;
                    case 'G':
                    case 'H':
                        if (parseInt($dateParts[i]) >= 0 && parseInt($dateParts[i]) <= 23){
                            parsedDatetime.hour = parseInt($dateParts[i]);
                        }
                        $time_flag = true;
                        break;
                    case 'i':
                        if (parseInt($dateParts[i]) >= 0 && parseInt($dateParts[i]) <= 59){
                            parsedDatetime.min = parseInt($dateParts[i]);
                        }
                        $time_flag = true;
                        break;
                    case 's':
                        if (parseInt($dateParts[i]) >= 0 && parseInt($dateParts[i]) <= 59){
                            parsedDatetime.sec = parseInt($dateParts[i]);
                        }
                        $time_flag = true;
                }    
            }
            
            if ($date_flag === true && parsedDatetime.year && parsedDatetime.month && parsedDatetime.day) {
                parsedDatetime.date = new Date(parsedDatetime.year, parsedDatetime.month - 1, parsedDatetime.day, parsedDatetime.hour, parsedDatetime.min, parsedDatetime.sec, 0);
                // parsedDatetime.dayOfWeek = parsedDatetime.date.getDay();
                return parsedDatetime.date;
            } else if ($time_flag === true) {
                parsedDatetime.date = new Date(0, 0, 0, parsedDatetime.hour, parsedDatetime.min, parsedDatetime.sec, 0);
                return parsedDatetime.date;
            } else {
                return false;
            }
        }
    },    
    enhDate: function($dateStr, $inpFormat){
        if (typeof $dateStr != 'string'){
            return $dateStr;
        }
        
        var parts = $dateStr.replace(this.separators, '\0').split('\0'),
            formatParts = $inpFormat.match(this.validParts),
            patt = /^[djmn]/g;
        
        if(patt.test(formatParts[0])){

            var $date = new Date(), 
                $dgt = 0, i    ;

            for (i = 0; i < parts.length; i++) {
                $dgt = 2;
                switch(i){
                    case 0:
                        if (formatParts[0] == 'm' || formatParts[0] == 'n') {
                            $date.setMonth(parseInt(parts[i].substr(0,2)) - 1);
                        } else {
                            $date.setDate(parseInt(parts[i].substr(0,2))); 
                        }
                        break;
                    case 1:
                        if (formatParts[0] == 'm' || formatParts[0] == 'n') {
                            $date.setDate(parseInt(parts[i].substr(0,2)));
                            
                        } else {
                            $date.setMonth(parseInt(parts[i].substr(0,2)) - 1); 
                        }
                        break;
                    case 2:
                        var year = $date.getFullYear();
                        
                        if (parts[i].length < 4){
                            $date.setFullYear(parseInt(year.toString().substr(0, 4 - parts[i].length) + parts[i]));
                            $dgt = parts[i].length;
                        } else {
                            $date.setFullYear = parseInt(parts[i].substr(0,4));
                            $dgt = 4;
                        }
                        break;
                    case 3:
                        $date.setHours(parseInt(parts[i].substr(0,2)));
                        break;
                    case 4:
                        $date.setMinutes(parseInt(parts[i].substr(0,2)));
                        break;
                    case 5:
                        $date.setSeconds(parseInt(parts[i].substr(0,2)));
                }            
                if (parts[i].substr($dgt).length > 0) {
                    parts.splice(i+1, 0, parts[i].substr($dgt));
                }
            }  // End for
            return $date;
        }
    },
    formatDate: function($date, $format){
        if(typeof $date == 'string'){
            $date = formatter.parseDate($date, $format);
            if ($date == false) return false;
        }
        if($date instanceof Date){
            var i, $char, $dateStr = '';
            var validParts= /[djDlwSFmMnyYaAgGhHisU]/;
            
            for(i = 0; i < $format.length; i++){
                $char = $format.charAt(i);
                
                if (validParts.test($char) == true){
                    switch ($char) {
                        case 'j':
                            $dateStr += $date.getDate();
                            break;
                        case 'd':
                            $dateStr += (($date.getDate() <= 9)? '0' : '') + $date.getDate();
                            break;
                        case 'D':
                            $dateStr += $dateTerms.shortDaysInWeek[$date.getDay()];
                            break;
                        case 'l':
                            $dateStr += $dateTerms.daysInWeek[$date.getDay()];
                            break;
                        case 'w':
                            $dateStr += $date.getDay();
                            break;
                        case 'S':
                            var $day = $date.getDay();
                            if ($day == 1 || $day == 21 || $day == 31){
                                $dateStr += 'st';
                            } else if ($day == 2 || $day == 22){
                                $dateStr += 'nd';
                            } else if ($day == 3 || $day == 23) {
                                $dateStr += 'rd';
                            } else {
                                $dateStr += 'th';
                            }
                            break;
                        case 'n':
                            $dateStr += $date.getMonth() + 1;
                            break;
                        case 'm':
                            $dateStr += (($date.getMonth() <= 8) ? '0' : '') + ($date.getMonth() + 1);
                            break;
                        case 'F':
                            $dateStr += $dateTerms.longMonthsInYear[$date.getMonth()];
                            break;
                        case 'M':
                            $dateStr += $dateTerms.shortMonthsInYear[$date.getMonth()];
                            break;
                        case 'Y':
                            $dateStr += $date.getFullYear();
                            break;
                        case 'y':
                            $dateStr += $date.getFullYear().substr(2);
                            break;
                        case 'g':
                            $dateStr += ($date.getHours() % 12) + 1;
                            break;
                        case 'G':
                            $dateStr += $date.getHours();
                            break;
                        case 'h':
                            $dateStr += (($date.getHours() % 12 <= 8) ? '0': '') + (($date.getHours() % 12) + 1);
                            break;
                        case 'H':
                            $dateStr += (($date.getHours() <= 9)? '0' : '') + $date.getHours();
                            break;
                        case 'a':
                        case 'A':
                            $dateStr += ($date.getHours <= 12) ? $dateTerms.meridium[0] : $dateTerms.meridium[1];
                            break;
                        
                        case 'i':
                            $dateStr += (($date.getMinutes() <= 9)? '0' : '') + $date.getMinutes();
                            break;
                        case 's':
                            $dateStr += (($date.getSeconds() <= 9)? '0' : '') + $date.getSeconds();
                            break;
                        case 'U':
                            $dateStr += $date.getMilliseconds() / 1000;
                            break;
                    }
                        
                } else {
                    $dateStr += $char;
                }
            }  // end for
            return $dateStr;
        }
    }
};


$.fn.datecontrol = function(options) {
    return this.each(function() {
        var $this = $(this);
        var pluginOptID = $this.data("datecontrolObj");
        
        var params = (isEmpty(options)) ? window[pluginOptID] : options;

      //  var $idDisp = $("#" + params.idDisp),
        var $idSave = $("#" + params.idSave),
            vUrl = params.url,
            vType = params.type,
            vDispFormat = params.dispFormat,
            vSaveFormat = params.saveFormat;
            $dateTerms = $.extend({}, $.fn.datecontrol.defaults, params.dateTerms);

        $this.on("change.datecontrol", function () {
            if (isEmpty($this.val())) {
                $idSave.val('');
            } else {
                if (isEmpty(vUrl)) {
                    var $dispDate = formatter.parseDate($this.val(), vDispFormat);
                    if ($dispDate == false){
                        $dispDate = formatter.enhDate($this.val(), vDispFormat);
                        $this.val(formatter.formatDate($dispDate, vDispFormat));
                    }
                    $idSave.val(formatter.formatDate($dispDate, vSaveFormat));
                } else {   
                    $.ajax({
                        url: vUrl,
                        type: "post",
                        dataType: "json",
                        data: {
                            displayDate: $this.val(),
                            type: vType,
                            dispFormat: vDispFormat,
                            saveFormat: vSaveFormat
                        },
                        success: function (data) {
                            if (data.status == "success") {
                                $idSave.val(data.output);
                            }
                        }
                    });
                }
            }
            $idSave.trigger("change");
        })

        $this.on("keydown.datecontrol", function (e) {
            if (isEmpty($this.val())) {
                $this.val('');
            } else {
                switch (e.keyCode) {
                case 27:
                    if ($.isFunction($this.parent().datepicker)){
                        $this.parent().datepicker("hide");
                        e.preventDefault;
                    }
                    if ($.isFunction($this.datepicker)){
                        $this.datepicker("hide");
                        e.preventDefault;
                    }
                    break;
                case 38:
                //case 56:
                //case 104:
                    e.preventDefault();
                    var $date = formatter.parseDate($this.val(), vDispFormat);
                        if ($date != false){
                            $date.setDate($date.getDate() + 1);
                        }
                    break;
                case 40:
                //case 50:
                //case 98:
                    e.preventDefault();
                    var $date = formatter.parseDate($this.val(), vDispFormat);
                        if ($date != false){
                            $date.setDate($date.getDate() - 1);
                        }
                    break;

                }
                if (e.keyCode == 38 || e.keyCode == 40){
                    $date = formatter.formatDate($date, vDispFormat);
                    $this.val($date);
                    $this.trigger("change");
                    if ($.isFunction($this.parent().datepicker)){
                        $this.parent().datepicker('update');
                    }
                    if ($.isFunction($this.datepicker)){
                        $this.datepicker('update');
                    }

                }
            }    
        }) // end key event
    })  // end this.each
};

$.fn.datecontrol.defaults = {
    daysInWeek:         ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    shortDaysInWeek:    ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    shortMonthsInYear:  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    longMonthsInYear:   ['January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'],
    meridium:           ['AM', 'PM']
};


}(jQuery));