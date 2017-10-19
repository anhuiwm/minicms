
//时间选择
function selecttime(flag) {
    if (flag == 1) {
        var endTime = $("#countTimeend").val();
        if (endTime != "") {
            WdatePicker({
                dateFmt: 'yyyy-MM-dd HH:mm',
                maxDate: endTime
            });
        } else {
            WdatePicker({
                dateFmt: 'yyyy-MM-dd HH:mm'
            });
        }
    } else {
        var startTime = $("#countTimestart").val();
        if (startTime != "") {
            WdatePicker({
                dateFmt: 'yyyy-MM-dd HH:mm',
                minDate: startTime
            });
        } else {
            WdatePicker({
                dateFmt: 'yyyy-MM-dd HH:mm'
            });
        }
    }
}


//时间选择
function selectdate(flag) {
if (flag == 1) {
    var endTime = $("#enddate").val();
    if (endTime != "") {
        WdatePicker({
            dateFmt: 'yyyy-M-d',
            maxDate: endTime
        });
    } else {
        WdatePicker({
            dateFmt: 'yyyy-M-d'
        });
    }
} else {
    var startTime = $("#startdate").val();
    if (startTime != "") {
        WdatePicker({
            dateFmt: 'yyyy-M-d',
            minDate: startTime
        });
    } else {
        WdatePicker({
            dateFmt: 'yyyy-M-d '
        });
    }
}
}

function selectonetime(flag) {
WdatePicker({
    dateFmt: 'yyyy-MM-dd HH:mm'
});
}

function selectdaytime(flag) {
WdatePicker({
    dateFmt: 'HH:mm:ss'
});
}
