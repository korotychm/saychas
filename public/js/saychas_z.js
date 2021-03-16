$(function() { 
    $(".phoneInput").mask("+7(999) 999-9999",{placeholder:" "});
    
    $("#dadataanswer").slideUp();
    
    $("#address").click(function(){$("#dadataanswer").slideUp(); $("#ycard").fadeOut();})
    
    $("#address").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function(suggestion) {
           $("#ycard").fadeIn();
           $("#dadataanswer").stop().html("<pre>"+print_r(suggestion)+"</pre>").slideDown();
           myMap.setCenter([suggestion.data.geo_lat,suggestion.data.geo_lon],16)
           var  placemark = new ymaps.Placemark([suggestion.data.geo_lat,suggestion.data.geo_lon], { balloonContent: 'я тут'}, )
           myMap.geoObjects.each(function (geoObject) {
               if (geoObject instanceof ymaps.Placemark) {
                   myMap.geoObjects.remove(geoObject);  
               }
           });
           myMap.geoObjects.add(placemark);  
           $("#geocoords").html(suggestion.data.geo_lat+","+suggestion.data.geo_lon);
        }
    });
    
    $("#tree").treeview({
        persist: "location",
        collapsed: true,
        animated: "medium",
     });
           
    $("#sendajax").click(function(){
        var dataString = $("#formajax").serialize();
        $.ajax({	
        // beforeSend : function (){ $("#overload").stop().show(); },
            url: "/ajax/toweb",
            type:'POST', 
            cache: false,	
            data: dataString,
            success: function(data){ $("#ajaxanswer").html(data);},
            error: function (xhr, ajaxOptions, thrownError) {$("#ajaxanswer").html("Ошибка соединения, попробуйте повторить попытку позже."+"\r\n " + xhr.status +" "+ thrownError );}
        });
    })
	   
    $("#sendajax2").click(function(){
        var dataString = $("#textarea").text();
        $.ajax({	
            url: "/receive",
            type:'POST', 
            cache: false,	
            data: dataString,
            success: function(data){ $("#ajaxanswer2").html(data);},
            error: function (xhr, ajaxOptions, thrownError) {$("#ajaxanswer2").html("Ошибка соединения, попробуйте повторить попытку позже."+"\r\n " + xhr.status +" "+ thrownError );}
        })
   })
	   
    $("#sendajaxprovider").click(function(){
        $("#waitprovider").fadeIn();
            $.ajax({	
            url: "/ajax/getproviders",
            cache: false,
            type:'POST',             
            success: function(data){ $("#provideranswer").html(data); $(".waiting").hide();},
            error: function (xhr, ajaxOptions, thrownError) {$("#provideranswer").html("Ошибка соединения, попробуйте повторить попытку позже."+"\r\n " + xhr.status +" "+ thrownError );  $(".waiting").hide(); }
            })
    });
    
    $(".provider-list").live("click", function(){
        $("#products").hide();
        $("#shops").show();
         $("#waitprovider").fadeIn();
        $("#waitshops").fadeIn();
        var providderId=$(this).attr("rel");
        $("#providershop").html($(this).text());
        $.ajax({	
            url: "/ajax/getshops",
            cache: false,	
            type:'POST',
            data: {'provider': providderId },
            success: function(data){ 
                $("#shopsanswer").html(data);
                $("#productsanswer").empty()
                $(".waiting").hide();
                return false;
             },
            error: function (xhr, ajaxOptions, thrownError) {$("#shopsanswer").html("Ошибка соединения, попробуйте повторить попытку позже."+"\r\n " + xhr.status +" "+ thrownError ); $(".waiting").hide(); return false; }
        });
    });
    $(".shop-list").live("click", function(){
        $("#waitproduct").fadeIn();
        $("#waitshops").fadeIn();
          $("#products").show();
        var providderId=$(this).attr("rel");
        $.ajax({	
            url: "/ajax/getproducts",
            cache: false,	
            type:'POST', 
            data: {'provider': providderId },
            success: function(data){ 
                $("#productsanswer").html(data);
                $(".waiting").hide();
                return false;
             },
            error: function (xhr, ajaxOptions, thrownError) {$("#ajaxanswer3").html("Ошибка соединения, попробуйте повторить попытку позже."+"\r\n " + xhr.status +" "+ thrownError ); $(".waiting").hide(); return false; }
        });
    });       
});

function print_r(arr, level) {
    var print_red_text = "";
    if(!level) level = 0;
    var level_padding = "";
    for(var j=0; j<level+1; j++) level_padding += "    ";
    if(typeof(arr) == 'object') {
        for(var item in arr) {
            var value = arr[item];
            if(typeof(value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value,level+1);
    } 
            else 
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    } 
    else  print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}
	