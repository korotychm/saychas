
function showAjaxErrorPopupWindow (status, error){
     showServicePopupWindow(
                            "Ошибка " + status,
                            "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + status + " " + error
                            );

}


function showServicePopupWindow(title, body, footer = "", noclose = false)
{
    $("#ServiceModalWindow .popup__close").removeClass('disabled');
    $("#ServiceModalWindow .popup__heading").html(title);
    $("#ServiceModalWindow #ServiceModalWraper").html(body);
    $("#ServiceModalWindow #ServiceModalWraper").append(footer);
    if (noclose) {
        $("#ServiceModalWindow .popup__close").addClass('disabled');
    }
    $("#ServiceModalWindow").fadeIn();
}


$(document).ready(function () {

    showBasket(0);
    getLegalStores($("#geodatadadata").text(), ".testlegalstor", false);
    //console.log($("#geodatadadata").text());


    $(".overcover").delay(500).fadeOut("slow");
    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };

    $("#test").click(function () {
        addUserAddrees();
    })

    $(".searchpanelclose").click(function () {
        $("#searchpanel").stop().css({top: "-200px"});
        $("#uadress").show();
    });

    $(".setuseraddress").click(function () {
        var rel = $(this).attr("rel");
        $.ajax({
            beforeSend: function () {},
            url: "/user-set-default-address",
            type: 'POST',
            cache: false,
            data: {'dataId': rel, 'reload': $(this).attr("data-reload")},
            success: function (data) {
                //console.log(data);
                //if(data.result == 1)
                //    $("#useradress-" + rel ).fadeOut();
                location = location.href;
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status !== 0) {
                    showServicePopupWindow(
                            "Ошибка " + xhr.status,
                            "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError
                            );
                }
                return false;
            }
        });
        return false;
    });

    $(".open-user-address-form").click(function () {
        $("#searchpanel").stop().animate({top: "0px"});
        $("#uadress").hide();
    });

    $("#tree22").treeview({
        persist: "location",
        collapsed: true,
        animated: "medium"
    });

    $("#dadataanswer").slideUp();

    $("#address").click(function () {
        $("#adesserror").hide();
        $("#dadataanswer").slideUp();
        $("#dadataask").delay(500).slideUp();
        $("#ycard").fadeOut();
    });

    $("#useraddress").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function (suggestion) {
            $("#adesserror").hide();
            //console.log(suggestion.data);
            if (!suggestion.data.house)
            {
                $("#useradesserror").html("Необходимо указать адрес до номера дома!").show();
                return false;
            }
            var dataString = JSON.stringify(suggestion);
            $("#geodatadadata").val(dataString);
            getLegalStores(dataString, '#useradesserror');
            addUserAddrees(dataString, $("#useraddress").val());
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

            //$("#yca rd").show();
            var dataString = JSON.stringify(suggestion);
            //var jsondata=JSON.parse(suggestion);
            $("#dadataask").html("<h4>Посланный запрос:</h4><pre>" + print_r(suggestion)/* dataString*/).stop().slideDown();
            getLocalStores(dataString, "#dadataanswer");
            $("#dadataanswer").stop().slideDown();
            $("#geocoords").html("<h3>GPS: " + suggestion.data.geo_lat + "," + suggestion.data.geo_lon + "</h3>");
            /*myMap.setCenter([suggestion.data.geo_lat, suggestion.data.geo_lon], 16)
             var placemark = new ymaps.Placemark([suggestion.data.geo_lat, suggestion.data.geo_lon], {balloonContent: 'я тут'}, )
             myMap.geoObjects.each(function (geoObject) {
             if (geoObject instanceof ymaps.Placemark) {
             myMap.geoObjects.remove(geoObject);
             }
             });/**/
            // myMap.geoObjects.add(placemark);
        }
    });

    $("#userAuthForm").submit(function () {
        var dataString = $("#userAuthForm").serialize();
        $.ajax({
            url: "/ajax/user-auth",
            cache: false,
            type: 'POST',
            dataType: 'json',
            data: dataString,
            success: function (data) {
                //console.log(data);
                if (data.error == false) {
                    //location = location.href;
                    getLegalStores($("#geodatadadata").text(), ".testlegalstor", true);
                    return false;
                }
                if (data.phone) {
                    if (data.isUser) {
                        $('.olduser').removeClass("none");
                        $('.newuser').addClass("none");
                        $('#oldUser').html(data.username);//.show();

                    } else {
                        $('.olduser').addClass("none");
                        $('.newuser').removeClass("none");
                    }
                }
                $("#userAuthError").html(data.message);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#userAuthError").html("Ошибка соединения, попробуйте повторить попытку позже." + "<hr> " + xhr.status + " " + thrownError);

            }
        });
        return false;
    });

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
        });
    });

    $(".provider-list").on("click", function () {
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
                $("#productsanswer").empty();
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

    $(".shop-list").on("click", function () {
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
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#ajaxanswer2").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function getLegalStores(dataString, obj = "#ajaxanswer2", wrelaoad = true) {
    $.ajax({
        //url: "/ajax/getstore",
        url: "/ajax-get-legal-store",
        type: 'POST',
        cache: false,
        data: {"value": dataString},
        success: function (data) {
            if (data.result) {
                //console.log(data.message);
                $(".errorblock").hide();
                $("#searchpanel").stop().css({top: "-100px"});
                $("#uadress").show();
                if (wrelaoad) {
                    location = location.href;
                    return false;
                }
            } else {
                $(obj).html(data.error);
                return true;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $(obj).html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function addUserAddrees(dadata = $("#geodatadadata").text(), address = $("#uadress span").text()) {
    $.ajax({
        url: "/ajax-add-user-address",
        type: 'POST',
        data: {'dadata': dadata, "address": address},
        dataType: 'json',
        cache: false,
        success: function (data) {
            //console.log(html);
            location = location.href;
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function setUserAddrees() {
    var dadata = $("#geodatadadata").text();
    var address = $("#uadress").text();

    $.ajax({
        url: "/ajax-set-user-address",
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function (html) {
            $(".user_address_set").html(html.userAddress);
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
    if (typeof (arr) === 'object') {
        for (var item in arr) {
            var value = arr[item];
            if (typeof (value) === 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value, level + 1);
            } else
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    } else
        print_red_text = "===>" + arr + "<===(" + typeof (arr) + ")";
    return print_red_text;
}


function showBasket(productadd = 0) {
    $.ajax({
        url: "/ajax/add-to-basket",
        cache: false,
        type: 'POST',
        //dataType: 'json',
        data: {"product": productadd},
        success: function (data) {
            //console.log(data);
            $("#bascetbottomblok .content ").empty();
            if (data.products) {
                $.each(data.products, function (key, value) {
                    var basket = "<div class='blok both relative'><img class='imgicon iblok' src='/images/product/" + value.image + "' ><span class='text'>" + value.name + "</span></div>";
                    $("#bascetbottomblok .content ").append(basket);
                });
            }
            $("#zakazcount").html(data.count); //data.total
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#bascetbottomblok .content ").html("Ошибка соединения " + xhr.status + ", попробуйте повторить попытку позже." + "<hr> " + xhr.status + " " + thrownError);
        }
    });
    return false;
}
