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
                showAjaxErrorPopupWindow (xhr.status, thrownError);
                return false;
            }
        });
    });

});
