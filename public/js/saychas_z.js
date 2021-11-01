$(document).ready(function () {

    getLegalStores($("#geodatadadata").text(), ".testlegalstor", false);
    //console.log($("#geodatadadata").text());

    $("#address").suggestions({
        token: "",
        type: "",
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
