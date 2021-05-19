
$(document).ready(function () {

    $("#tree").delay(500).slideDown("slow");
    $(".overcover").delay(500).fadeOut("slow");
    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };
});
$(function () {
    
    $('#banzaii').on('click', function(){
        $.ajax({
            url: "/banzaii",
            type: 'POST',
            cache: false,
            data: null,
            success: function (data) {
                $("#vonzaii").html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#vonzaii").html('Error');
                alert('asdf');
            }
        });
    });

    function hidefilteritem() {
        $(".filtritem").removeClass("active");
        $(".filtritemcontent").hide();
        // $(".filtritemtitle").removeClass("closefilteritem");        
    }
    
    function sendfilterform (){
        var dataString = $("#filtrform").serialize();
        $.ajax({
            // beforeSend : function (){ $("#overload").stop().show(); },
            url: "/ajax-fltr",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                // $("#ajaxfiltranswer").html(data);
                window.location.href = window.location.href
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ajaxfiltranswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            }
        });
    }
    
    

    $(".product-page-image").click(function(){
        var newsrc=$(this).attr("src");
        var parent = $(this).parent();
        var oldsrc=$("#productimage0").attr("src");
        $(".product-image-container-mini").removeClass("borderred");
        parent.addClass("borderred"); 
        $("#productimage0").attr("src", newsrc);
        //$(this).attr("src", oldsrc);
        return false;
    }); 

    $(".checkgroup").click(function () {
        
    console.log("#fltrcheck"+$(this).attr("for"));
        if($(this).hasClass("zach")) {$(this).removeClass("zach"); $("#fltrcheck"+$(this).attr("for")).prop("checked", false);}
        else  {$(this).addClass("zach"); $("#fltrcheck"+$(this).attr("for")).prop("checked", true);}
    });
    $(".closefilteritem").live("click", function () {
        hidefilteritem();
    });
    $(".filtritemtitle").click(function () {


        // if($(this).hasClass("closefilteritem")) {hidefilteritem(); return;}
        hidefilteritem();
        var id = $(this).attr("rel");
        $("#fi" + id).addClass("active");
        $("#fc" + id).slideDown();
        // $(this).addClass("closefilteritem");


    })
    $(".searchpanelclose").click(function () {
        $("#searchpanel").stop().css({top: "-200px"});
        $("#uadress").show();
    })
    $(".open-user-address-form").click(function () {
          $("#searchpanel").stop().animate({top: "0px"});  
        $("#uadress").hide();
    })
    $("#tree22").treeview({
        persist: "location",
        collapsed: true,
        animated: "medium",

    });

    $(".phoneInput").mask("+7(999) 999-9999", {placeholder: " "});

    $("#dadataanswer").slideUp();

    $("#address").click(function () {
        $("#adesserror").hide();
        $("#dadataanswer").slideUp();
        $("#dadataask").delay(500).slideUp();
        $("#ycard").fadeOut();
    })

    $("#useraddress").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function (suggestion) {
            $("#adesserror").hide();
            if (!suggestion.data.house)
            {
                $("#useradesserror").html("Необходимо указать адрес до номера дома!").show();
                return false;
            }


            //return ;
            var dataString = JSON.stringify(suggestion);
            getLegalStores(dataString, '#useradesserror');
        }
    });

    $("#address").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function (suggestion) {
            $("#adesserror").hide();
            if (!suggestion.data.house)
            {
                $("#adesserror").html("Необходимо указать адрес до номера дома!").show();
            }

            $("#yca rd").show();
            var dataString = JSON.stringify(suggestion);
            //var jsondata=JSON.parse(suggestion);
            $("#dadataask").html("<h4>Посланный запрос:</h4><pre>" + print_r(suggestion)/* dataString*/).stop().slideDown();
            getLocalStores(dataString, "#dadataanswer");
            $("#dadataanswer").stop().slideDown();


            myMap.setCenter([suggestion.data.geo_lat, suggestion.data.geo_lon], 16)
            var placemark = new ymaps.Placemark([suggestion.data.geo_lat, suggestion.data.geo_lon], {balloonContent: 'я тут'}, )
            myMap.geoObjects.each(function (geoObject) {
                if (geoObject instanceof ymaps.Placemark) {
                    myMap.geoObjects.remove(geoObject);
                }
            });/**/
            myMap.geoObjects.add(placemark);
            $("#geocoords").html("<h3>GPS: " + suggestion.data.geo_lat + "," + suggestion.data.geo_lon + "</h3>");
        }
    });



    $("#tree").treeview({
        persist: "location",
        collapsed: true,
        animated: "medium",
    });

    $("#sendajax").click(function () {
        var dataString = $("#formajax").serialize();
        $.ajax({
            // beforeSend : function (){ $("#overload").stop().show(); },
            url: "/ajax-to-web",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                $("#ajaxanswer").html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ajaxanswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            }
        });
    })

    $(".formsend").live("change", function () {
        sendfilterform();
    })

     $(".formsendbutton").live("click", function () {
        sendfilterform();
    })

    $("#sendajax2").click(function () {
        var dataString = $("#textarea").val();
        getLocalStores(dataString);
    })

    $("#sendajaxprovider").click(function () {
        $("#waitprovider").show();
        $.ajax({
            url: "/ajax/getproviders",
            cache: false,
            type: 'POST',
            success: function (data) {
                $("#provideranswer").html(data);
                $(".waiting").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#provideranswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
                $(".waiting").hide();
            }
        })
    });

    $(".provider-list").live("click", function () {
        $("#products").hide();
        $("#shops").show();
        $("#waitprovider").show();
        $("#waitshops").show();
        var providderId = $(this).attr("rel");
        $("#providershop").html($(this).text());
        $.ajax({
            url: "/ajax/getshops",
            cache: false,
            type: 'POST',
            data: {'provider': providderId},
            success: function (data) {
                $("#shopsanswer").html(data);
                $("#productsanswer").empty()
                $(".waiting").hide();
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#shopsanswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
                $(".waiting").hide();
                return false;
            }
        });
    });
    $(".shop-list").live("click", function () {
        $("#waitproduct").show();
        $("#waitshops").show();
        $("#products").show();
        var shopId = $(this).attr("rel");
        $.ajax({
            url: "/ajax/getproducts",
            cache: false,
            type: 'POST',
            data: {'shop': shopId},
            success: function (data) {
                $("#productsanswer").html(data);
                $(".waiting").hide();
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ajaxanswer3").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
                $(".waiting").hide();
                return false;
            }
        });
    });

$(window).resize(function(){leftpanelclose();});
	$(".catalogshow").click(function(){
		$("#overcoverblack").fadeIn();
		$("#lefmobiletpanel").animate({left:"0" },500);
		
	})	
	$("#lefmobiletpanelclose").click(function(){leftpanelclose()})
	
$(".spoileropenlink").click(function(){
    var id=$(this).attr("rel");
    $("#spoiler-show-" + id).show();
    $("#spoiler-hide-" + id).hide();
    
    
    return false;    
})

});

function getLocalStores(dataString, obj = "#ajaxanswer2") {
    $.ajax({
        //url: "/ajax/getstore",
        url: "/ajax-get-store",
        type: 'POST',
        cache: false,
        data: {"value": dataString},
        success: function (data) {
            $(obj).html(data);
            return true
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#ajaxanswer2").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}
function getLegalStores(dataString, obj = "#ajaxanswer2") {
    $.ajax({
        //url: "/ajax/getstore",
        url: "/ajax-get-legal-store",
        type: 'POST',
        cache: false,
        data: {"value": dataString},
        success: function (data) {
            if (data == "200") {
                $(".errorblock").hide();
                $("#searchpanel").stop().css({top: "-100px"});
                $("#uadress").show();
                window.location.href = window.location.href;
                return setUserAddrees();
            }

            $(obj).html(data);
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $(obj).html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function leftpanelclose(){
		$("#overcoverblack").fadeOut();
		$("#lefmobiletpanel").stop().animate({left:"-110%" },300);
	}

function setUserAddrees() {
    $.ajax({
        url: "/ajax-set-user-address",
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function (html) {
            console.log(html.legalStore);
            $(".user_address_set").html(html.userAddress);
            $(".testlegalstor").html("<h2>доступные магазины</h2></pre>" + print_r(html.legalStore) + "</pre>");//.css("border:1px soli red"); 
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function print_r(arr, level) {
    var print_red_text = "";
    if (!level)
        level = 0;
    var level_padding = "";
    for (var j = 0; j < level + 1; j++)
        level_padding += "    ";
    if (typeof (arr) == 'object') {
        for (var item in arr) {
            var value = arr[item];
            if (typeof (value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value, level + 1);
            } else
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    } else
        print_red_text = "===>" + arr + "<===(" + typeof (arr) + ")";
    return print_red_text;
}
	