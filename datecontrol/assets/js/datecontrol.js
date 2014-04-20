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

function isEmpty(value, trim) {
    return value === null || value === undefined || value == []
        || value === '' || trim && $.trim(value) === '';
}

function parseDateControl(params) {
    var $idDisp = $("#" + params.idDisp),
        $idSave = $("#" + params.idSave),
        vUrl = params.url,
        vType = params.type,
        vDispFormat = params.dispFormat,
        vSaveFormat = params.saveFormat;

    $idDisp.on("change", function () {
        if (isEmpty($idDisp.val())) {
            $idSave.val('');
        }
        else {
            $.ajax({
                url: vUrl,
                type: "post",
                dataType: "json",
                data: {
                    displayDate: $idDisp.val(),
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
        $idSave.trigger("change");
    });

}