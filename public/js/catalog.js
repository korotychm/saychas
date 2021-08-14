function getCategoryFilters(categoryId){
      $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-category-filters",
            type: 'POST',
            cache: false,
            data: {"categoryId": categoryId},
            success: function (data) {
              showServicePopupWindow("Фильтры дла каталога",JSON.stringify(data));
              return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
             if (xhr.status !== 0) {
                    showServicePopupWindow(
                        "Ошибка " + xhr.status,
                        "<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>"
                    );
                }    
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
});