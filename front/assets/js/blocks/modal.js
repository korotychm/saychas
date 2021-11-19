function showAjaxErrorPopupWindow (status, error){
     showServicePopupWindow(
                            "Ошибка " + status,
                            "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + status + " " + error
                            );

}


function showServicePopupWindow(title, body, footer = "", noclose = false)
{
    $("#ServiceModalWindow .popup__close").removeClass('disabled');
    $("#ServiceModalWindow .popup__heading").html(title);
    $("#ServiceModalWindow #ServiceModalWraper").html(body);
    $("#ServiceModalWindow #ServiceModalWraper").append(footer);
    if (noclose) {
        $("#ServiceModalWindow .popup__close").addClass('disabled');
    }
    $("#ServiceModalWindow").fadeIn();
}
