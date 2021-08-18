function getProdeuctCategory(){
    var categoryId = window.location.href.split("/").slice(-1)[0];  
    $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-products-categories",
            type: 'POST',
            cache: false,
            data: {"categoryId": categoryId},
            success: function (data) {
             //showAjaxErrorPopupWindow ("id", categoryId );
             $("#category-list").html(JSON.stringify(data,true,2));
              return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
             if (xhr.status !== 0) {
                    showAjaxErrorPopupWindow (xhr.status, thrownError);
                }    
                return false;
            }
        });
    return false;    
}

$(document).ready(function () {
  getProdeuctCategory();
    
});