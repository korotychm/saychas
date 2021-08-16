$(document).ready(function () {
    
    $(".deleteuseraddress").click(function () {
        var rel = $(this).attr("rel");
        $.ajax({
            beforeSend: function () {},
            url: "/user-delete-address",
            type: 'POST',
            cache: false,
            data: {'dataId': rel, 'reload': $(this).attr("data-reload")},
            success: function (data) {
                //if(data.result == 1)
                $("#useradress-" + rel).fadeOut();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                $("#ServiceModalWindow .modal-title").html("Ошибка deleteuseraddress " + xhr.status);
                $("#ServiceModalWindow #ServiceModalWraper").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#ServiceModalWindow").modal("show");
                return false;
            }
        });
    });

});
