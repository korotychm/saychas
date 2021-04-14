
$(document).ready(function () {

    $("#tree").delay(500).slideDown("slow");
    $(".overcover").delay(500).fadeOut("slow");
    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };
});
$(function () {


    function hidefilteritem() {
        $(".filtritem").removeClass("active");
        $(".filtritemcontent").hide();
        // $(".filtritemtitle").removeClass("closefilteritem");        
    }

    $(".product-page-image").click(function(){
        var newsrc=$(this).attr("src");
        var oldsrc=$("#productimage0").attr("src");
        $(".product-page-image").removeClass("borderred");
        $(this).addClass("borderred"); 
        $("#productimage0").attr("src", newsrc);
        $(this).attr("src", oldsrc);
        return false;
    }); 

    $(".checkgroup").click(function () {
        if($(this).hasClass("zach")) {$(this).removeClass("zach"); $("#check"+$(this).attr("for")).val("");}
        else  {$(this).addClass("zach"); $("#fltrcheck"+$(this).attr("for")).val($(this).attr("for"));}
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
    $(".open-user-address-form, .searchpanelclose").click(function () {
        $("#searchpanel").slideToggle()
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
        var dataString = $("#filtrform").serialize();
        $.ajax({
            // beforeSend : function (){ $("#overload").stop().show(); },
            url: "/ajax-fltr",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                window.location.href = window.location.href
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ajaxfiltranswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            }
        });
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
                $("#searchpanel").slideUp();
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
	