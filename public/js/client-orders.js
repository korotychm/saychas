function getClientOrders(){
    $.ajax({
            beforeSend: function () {},
            url: "/ajax-get-order-list",
            type: 'POST',
            cache: false,
            success: function (data) {
                //if(data.result == 1)
                $("#order-list").html(JSON.stringify(data,  null, 2 ));
            },
            error: function (xhr, ajaxOptions, thrownError) {
              showServicePopupWindow(
                            "Ошибка " + xhr.status,
                            "<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>"
                            );
                return false;
            }
        });
}

$(document).ready(function () {
    getClientOrders();
});
