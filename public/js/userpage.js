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
    })

    /*$(".setuseraddress").click(function () {
        var rel = $(this).attr("rel");
        $.ajax({
            beforeSend: function () {},
            url: "/user-set-default-address",
            type: 'POST',
            cache: false,
            data: {'dataId': rel, 'reload': $(this).attr("data-reload")},
            success: function (data) {
                //console.log(data);
                //if(data.result == 1)
                //    $("#useradress-" + rel ).fadeOut();
                location = location.href;
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
if (xhr.status != 0) {
                $("#ServiceModalWindow .modal-title").html("Ошибка setuseraddress " + xhr.status);
                $("#ServiceModalWindow #ServiceModalWraper").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#ServiceModalWindow").modal("show");
            }
                return false;
            }
        });
    })*/

})
