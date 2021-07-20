
//ajax/calculate-basket-item
function calculateBasketHeader (productId)
{
    var totalshops = 0,  totalproduct = 0; 
    
    $.each($(".basketproviderblok"), function(index,value){
      var id = $(this).attr("id"), rel = $(this).attr("rel");
      var products = $("#" + id + " .checkallprovider.zach ").length ;
    console.log($("#" + id + " .checkallprovider ").length + " / " + $("#" + id + " .checkallprovider.zach ").length + " / #checkallallprovider" + rel);
        
        if ($("#" + id + " .checkallprovider.zach ").length == $("#" + id + " .checkallprovider").length ){
          
          
          $("#checkallallprovider-" + rel ).addClass("zach");
      }
      
      if ($(".checkprovider.zach ").length == $(".checkprovider").length ){
          $("#checkallavailble").addClass("zach");
      }
      
      
      if  (products > 0) {totalshops ++ ; totalproduct +=products; 
        $("#" + id + " .basketrowselfdelevery").show();
        }
      else {
          //console.log(id + ":" +products );
            //console.log($("#selfdeleveryonoff-" + rel));
            $("#selfdeleveryonoff-" + rel).removeClass("zach");
            $("#" + id + " .basketrowselfdelevery").hide();
            
            $("#selfdeleverycheckbox-" + rel).prop("checked", false);
            $("#providerblok-" + rel).removeClass("goself");
            $("#provider_addressappend" + rel).hide();
            $("#seldeleveryblokrow-" + rel).removeClass('seldeleveryblokrowcountme').hide();
          
          
      }
      //console.log(id + ":" +products );
      
    })
    
    var h1 = "";
    if (totalproduct < 1) h1 ="Товары не выбраны";
    else {
        if (totalproduct == 1 ) h1 = totalproduct + " наименование ";
        else if (totalproduct > 1 &&  totalproduct < 5 ) h1 = totalproduct + " наименования ";
        else h1 = totalproduct + " наименований ";
        if (totalshops == 1) h1 += " из " + totalshops + " магазина ";
        else h1 += " из " + totalshops + " магазинов ";
    }
    $("#h1title").text(h1);//console.log("Магазинов" + totalshops + "; продуктов " + totalproduct );
}

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

function calculateSelfDelevery ()
{
    var store = $(".seldeleveryblokrowcountme").length;
    var product = $(".seldeleveryblokrowcountme .selfdeleverycountme").length
    //console.log();
    if(store > 0) $("#selfdeleverymainblok").show(); else $("#selfdeleverymainblok").hide();
    $("#selfdeleverycountproduct").html(product);
    $("#selfdeleverycountstore").html(store);
}


function calculateBasketMerge (dataString)
{
    
    $.ajax({
            beforeSend : function (){ $("#basket-ordermerge-cover").stop().fadeIn(); },
            
            url: "/ajax-basket-order-merge",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                $("#basketordermerge").html(data);
                $("#basket-ordermerge-cover").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#basketordermerge").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#basket-ordermerge-cover").hide();
                return false;
            }
        });
    
}

function calculateBasketPayCard ()
{
    var dataString = $("#user-basket-form").serialize();
    $.ajax({
            url: "/ajax-basket-pay-card-info",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                $("#baskepaycardinfo").html(data);
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#baskepaycardinfo").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                
                return false;
            }
        });
    
}





function loadPayInfo(){
    var dataString = $("#user-basket-form").serialize();
    $.ajax({
            beforeSend : function (){ 
                $("#basket-payinfo-cover").stop().fadeIn(); 
                calculateBasketPayCard();
                calculateBasketHeader();
                calculateBasketMerge (dataString);
                calculateSelfDelevery();
                },
            url: "/ajax-basket-pay-info",
            type: 'POST',
            cache: false,
            data: $("#user-basket-form").serialize(),
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
    
    //$("#user-basket-form").serialize()
    loadPayInfo();
    $("body").on("click dblclick", ".radiomergebut, .loadpayinfo", function(){loadPayInfo()});
    $("body").on("click dblclick", ".countproductminus", function(){
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
    $("body").on("click dblclick", ".countproductplus", function(){
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
                $("#seldeleveryblokrow-" + rel).removeClass('seldeleveryblokrowcountme').hide();
                
        } else {
                $('.selfdeleveryonoff[rel^='+rel+']').removeClass("zach");
                $('.relcheck[rel^='+rel+']').prop("checked", false);;    
                $(this).addClass("zach");
                $("#selfdeleverycheckbox-" + rel).prop("checked", true);
                
                $("#providerblok-" + rel).addClass("goself");
                $("#provider_addressappend" + rel).show();
                $("#seldeleveryblokrow-" + rel).addClass('seldeleveryblokrowcountme').show();
        }
        loadPayInfo();
    });
    $("#checkallavailble").click(function(){
        if($(this).hasClass("zach")){
            $(".allall").removeClass("zach");
            $("#checkallavailble").removeClass("zach");
            $(".allallcheck").prop("checked", false);
            $(".selfdeleveryallall").removeClass("selfdeleverycountme").hide();
            
        }
        else {
            $("#checkallavailble").addClass("zach");
            $(".allall").addClass("zach");
            $(".allallcheck").prop("checked", true);
            $(".selfdeleveryallall").addClass("selfdeleverycountme").show();
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
             $(".selfdeleveryallprovider-" + rel ).removeClass("selfdeleverycountme").hide();
        }
        else {
            $(this).addClass("zach");
            $(".provider-"+rel).addClass("zach");
            $(".povidercheck-" + rel ).prop("checked", true);
            $(".selfdeleveryallprovider-" + rel ).addClass("selfdeleverycountme").show();
        }
        loadPayInfo();
    })
    
    $(".basketrow .deleteproduct").click(function(){
        var productId= $(this).attr("rel");
        var provider = $(this).attr("provider");
        
        //console.log($("#providerblok-" + provider + " .basketrowproduct").length);
       /* */$.ajax({
            beforeSend : function (){ 
                },
            url: "/ajax/del-from-basket",
            type: 'POST',
            cache: false,
            data: {"productId": productId},
            success: function (data) {
                //console.log(data);
                $("#basketrow-" + productId).remove();
                if($("#providerblok-" + provider + " .basketrowproduct").length < 1){
                    $("#providerblok-" + provider).remove();
                }
                loadPayInfo();
                //console.log($("#providerblok-" + provider + " .basketrowproduct").lenght);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                $("#basket-payinfo").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
                
                
            }
        });/**/
    })
    //timepoint-dostmergeon
    
    
     $(".checkallprovider").click(function(){
        
         var rel=$(this).attr('rel');
         var provider=$(this).attr('provider');
         $("#checkallavailble").removeClass("zach");
         $("#checkallallprovider-"+ provider).removeClass("zach");
        if($(this).hasClass("zach")){
            $(this).removeClass("zach");
            $(".product-" + rel ).prop("checked", false);
            $(".selfdeleveryproduct-" + rel ).removeClass("selfdeleverycountme").hide();
            
        }
        else {
            $(this).addClass("zach");
            $(".product-" + rel ).prop("checked", true);
            $(".selfdeleveryproduct-" + rel ).addClass("selfdeleverycountme").show();
        }
        loadPayInfo();
    })

})
