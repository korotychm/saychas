function getCategoryFilters(categoryId){
      $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-category-filters",
            type: 'POST',
            cache: false,
            data: {"categoryId": categoryId},
            success: function (data) {
               $("#ServiceModalWindow .modal-title").html("Фильтры дла каталога");
               $("#ServiceModalWindow #ServiceModalWraper").html(JSON.stringify(data));
               $("#ServiceModalWindow").modal("show");
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ServiceModalWindow .modal-title").html("Ошибка checkBasketDataBeforeSend" +  xhr.status );
                $("#ServiceModalWindow #ServiceModalWraper").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#ServiceModalWindow").modal("show");
                return false;
            }
        });
    return false;
    
}

$(document).ready(function () {
    sendfilterform() ;
    $("#testFiltersButton").click(function(){
        getCategoryFilters($("#testFiltersCategotyId").val());
    });
})