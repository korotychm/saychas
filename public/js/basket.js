
//ajax/calculate-basket-item
function calculateBasketItem (productId)
{
    var count=$("#countprhidden-"+ productId).val();
    $.ajax({
            url: "/ajax/calculate-basket-item",
            cache: false,
            type: 'POST',
            //dataType: 'json',
            data:{"product" : product, "count":count },
            success: function (data) {
                console.log(data);
             },
             error: function (xhr, ajaxOptions, thrownError) {
             console.log("Ошибка соединения " + xhr.status + "" + "<hr> " + xhr.status + " " + thrownError);
                
            }
         })
         return false;
}
$(function(){
    $(".countproductminus").live("click", function(){
        if($(this).hasClass("disabled")) return false;
        var id=$(this).attr("rel");
        var count=count=($("#countprhidden-"+ id).val())*1;
        var newcount=count - 1;
        
        if (count <= 1) {newcount = 1; $(this).addClass("disabled");  }
        $("#countprhidden-"+ id).val(newcount);
        $("#countproductnum-"+ id).html(newcount);
    })
})
