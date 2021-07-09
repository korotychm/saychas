
//ajax/calculate-basket-item
function calculateBasketItem (productId)
{
    var count=$("#countprhidden-"+ productId).val();
    $.ajax({
            url: "/ajax/calculate-basket-item",
            cache: false,
            type: 'POST',
            dataType: 'json',
            data:{"product" : productId, "count":count },
            success: function (data) {
              console.log(data);
              $("#priceproduct-"+ productId).html(data.totalFomated);
                
             },
             error: function (xhr, ajaxOptions, thrownError) {
             console.log("Ошибка соединения " + xhr.status + "" + "<hr> " + xhr.status + " " + thrownError);
                
            }
         })
    
}


function loadPayInfo(){
    var dataString = $("#user-basket-form").serialize();
    $.ajax({
            beforeSend : function (){ $("#basket-payinfo-cover").stop().fadeIn(); },
            url: "/ajax-basket-pay-info",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                $("#basket-payinfo").html(data);
                $("#basket-payinfo-cover").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#basket-payinfo").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#basket-payinfo-cover").stop().hide();
            }
        });
} 


$(function(){
    loadPayInfo();
    $(".countproductminus").live("click dblclick", function(){
        if($(this).hasClass("disabled")) return false;
        
        var id=$(this).attr("rel");
        var max = ($("#countproductnum-"+ id).attr("max"))*1;
        var count=count=($("#countprhidden-"+ id).val())*1;
        var newcount=count - 1;
        
        if (newcount <= 1) {
            newcount = 1; $(this).addClass("disabled");  
        }
        $("#countprhidden-"+ id).val(newcount);
        
        $("#countproductnum-"+ id).html(newcount);
        $('.countproductplus[rel^='+id+']').removeClass("disabled");
        calculateBasketItem (id);
        loadPayInfo();
        return false
    })
    $(".countproductplus").live("click dblclick", function(){
        if($(this).hasClass("disabled")) return false;
        var id=$(this).attr("rel");
        var max = ($("#countproductnum-"+ id).attr("max"))*1;
        var count = ($("#countprhidden-"+ id).val())*1;
        var newcount = count + 1;
        if (newcount > 1) $('.countproductminus[rel^='+id+']').removeClass("disabled");
        if (newcount >= max ) {
            newcount = max; $(this).addClass("disabled");  
        }
        $("#countprhidden-"+ id).val(newcount);
        $("#countproductnum-"+ id).html(newcount);
        calculateBasketItem (id);
        loadPayInfo();
        return false
    })
    $(".selfdeleveryonoff").click(function () {
        var rel=$(this).attr('rel');
        //console.log(".fltrcheck" + $(this).attr("for"));
        if ($(this).hasClass("zach")) {
                $(this).removeClass("zach");
                $("#selfdeleverycheckbox-" + rel).prop("checked", false);
                $("#providerblok-" + rel).removeClass("goself");
                $("#provider_addressappend" + rel).hide();
        } else {
                $('.selfdeleveryonoff[rel^='+rel+']').removeClass("zach");
                $('.relcheck[rel^='+rel+']').prop("checked", false);;    
                $(this).addClass("zach");
                $("#selfdeleverycheckbox-" + rel).prop("checked", true);
                
                $("#providerblok-" + rel).addClass("goself");
                $("#provider_addressappend" + rel).show();
        }
        loadPayInfo();
    });
    $("#checkallavailble").click(function(){
        if($(this).hasClass("zach")){
            $(".allall").removeClass("zach");
            $("#checkallavailble").removeClass("zach");
            $(".allallcheck").prop("checked", false);
            
        }
        else {
            $("#checkallavailble").addClass("zach");
            $(".allall").addClass("zach");
            $(".allallcheck").prop("checked", true);
        }
        loadPayInfo();
    })
    $(".checkallallprovider").click(function(){
        $("#checkallavailble").removeClass("zach");
         var rel=$(this).attr('rel');
        if($(this).hasClass("zach")){
            $(this).removeClass("zach");
            $(".provider-"+rel).removeClass("zach");
            $(".povidercheck-" + rel ).prop("checked", false);
        }
        else {
            $(this).addClass("zach");
            $(".provider-"+rel).addClass("zach");
            $(".povidercheck-" + rel ).prop("checked", true);
        }
        loadPayInfo();
    })
    
     $(".checkallprovider").click(function(){
        
         var rel=$(this).attr('rel');
         var provider=$(this).attr('provider');
         $("#checkallavailble").removeClass("zach");
         $("#checkallallprovider-"+ provider).removeClass("zach");
        if($(this).hasClass("zach")){
            $(this).removeClass("zach");
            $(".product-" + rel ).prop("checked", false);
        }
        else {
            $(this).addClass("zach");
            $(".product-" + rel ).prop("checked", true);
        }
        loadPayInfo();
    })

})
