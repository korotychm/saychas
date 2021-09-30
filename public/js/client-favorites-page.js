function getClientProducts (type){
    var url = "/ajax-get-client-" + type;
     $.ajax({
            url: url,
            success: function (data) {
             $("#client-" + type ).html(JSON.stringify(data,  null, 2 ));
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
}

$(function () {
    getClientProducts ("favorites");
    getClientProducts ("history");
});

