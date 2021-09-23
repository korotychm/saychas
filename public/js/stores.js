function getProdeuctCategory(){
    var storeId = $("#baseId").val(); 
    var categoryId = $("#categoryId").val(); 
    
    //$("#quick-menu-item-" + categoryId ).addClass("active");
    $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-products-store",
            type: 'POST',
            cache: false,
            data: {"storeId": storeId, "categoryId": categoryId },
            success: function (data) {
             //showAjaxErrorPopupWindow ("id", categoryId );
             $("#storecontent").html(JSON.stringify(data,true,2));
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