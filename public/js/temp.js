function addProductToFavorites (productId, callback) {
     $.ajax({
            url: "/ajax/add-to-favorites",
            cache: false,
            type: 'POST',
            data: {'productId': productId},
            success: function (data) {
                console.log(data);
                if (!data.result) {showAjaxErrorPopupWindow("", data.description ); return;}
                callback.addClass("remove");
                callback.children("span").text(data.lable);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
  }
 function removeProductFromFavorites (productId, callback) {
     $.ajax({
            url: "/ajax/remove-from-favorites",
            cache: false,
            type: 'POST',
            data: {'productId': productId},
            success: function (data) {
                console.log(data);
                if (!data.result) {showAjaxErrorPopupWindow("", data.description ); return;}
                callback.removeClass("remove");
                callback.children("span").text(data.lable);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
  } 


$(document).ready(function () {
    
    // Самовывоз из магазина
$(document).on('click', '.panel__to-wishlist',function(){
//$('.panel__to-wishlist').click(function(){    
    var callback = $(this);
    if (callback.hasClass("remove")){
        removeProductFromFavorites(callback.attr("rel"), callback);        
    }
    else {
        addProductToFavorites(callback.attr("rel"), callback);        
        
    }
    
});


    
    

  $("#tree").delay(500).slideDown("slow");

  $("#tree").treeview({
      persist: "location",
      collapsed: true,
      animated: "medium"
  });

  $(window).resize(function () {
      leftpanelclose();
  });

  $(".catalogshow").click(function () {
      $("#overcoverblack").fadeIn();
      $("#lefmobiletpanel").animate({left: "0"}, 500);

  });
  
  
  

  $("#lefmobiletpanelclose").click(function () {
      leftpanelclose();
  });

  function leftpanelclose() {
      $("#overcoverblack").fadeOut();
      $("#lefmobiletpanel").stop().animate({left: "-110%"}, 300);
  }

});
