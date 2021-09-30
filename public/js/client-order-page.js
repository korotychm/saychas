function getClientOrder(){
    var orderId = window.location.href.split("/").slice(-1)[0]; 
    $.ajax({
            beforeSend: function () {},
            url: "/ajax-get-order-page",
            type: 'POST',
            cache: false,
            data:{"orderId":orderId},
            success: function (data) {
                //if(data.result == 1)
                $("#client-order").html(JSON.stringify(data,  null, 2 ));
            },
            error: function (xhr, ajaxOptions, thrownError) {
              showServicePopupWindow(
                            "Ошибка " + xhr.status,
                            "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError
                            );
                return false;
            }
        });
}

$(document).ready(function () {
    getClientOrder();
});



