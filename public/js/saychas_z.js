function sendfilterform() {
        //alert("!!!!");
        var dataString = $("#filtrform").serialize();
        $.ajax({
            beforeSend : function (){ 
                $("#overload").stop().show(); 
                
            },
            url: "/ajax-fltr",
            type: 'POST',
            cache: false,
            data: dataString,
            success: function (data) {
                
                $("#tovar-list").html(data);
                //window.location.href = window.location.href
                //alert("!!!!234");
            },
            error: function (xhr, ajaxOptions, thrownError) {
               $("#ServiceModalWindow .modal-title").html("Ошибка " +  xhr.status );
               $("#ServiceModalWindow #ServiceModalWraper").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
               $("#ServiceModalWindow").modal("show");
            }
        });
    }
$(document).ready(function () {

    showBasket(0);
    getLegalStores($("#geodatadadata").text(), ".testlegalstor", false);
    //console.log($("#geodatadadata").text());
    
    $("#tree").delay(500).slideDown("slow");
    $(".overcover").delay(500).fadeOut("slow");
    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };
});
$(function () {

$("#test").click(function(){addUserAddrees();})

$("body").on("keyUp, blur, focus, change", ".numonly", function(){$(this).val($(this).val().replace (/[^0-9+]/g, ''));})


    show_scrollTop();
    $(window).scroll(function () {
        show_scrollTop();
    });

    $("#quicktop").click(function () {
        $("html:not(:animated)").animate({scrollTop: 0}, 500);
        return false;
    });



    function hidefilteritem() {
        /*$(".filtritem").removeClass("active");
        $(".filtritemcontent").hide();
        // $(".filtritemtitle").removeClass("closefilteritem");        */
    }

    



    $(".product-page-image").click(function () {
        var newsrc = $(this).attr("src");
        var parent = $(this).parent();
        var oldsrc = $("#productimage0").attr("src");
        $(".product-image-container-mini").removeClass("borderred");
        parent.addClass("borderred");
        $("#productimage0").attr("src", newsrc);
        //$(this).attr("src", oldsrc);
        return false;
    });

    $("body").on("click",".checkgroup", function () {

        console.log(".fltrcheck" + $(this).attr("for"));
        if ($(this).hasClass("zach")) {
                $(this).removeClass("zach");
                $(".fltrcheck" + $(this).attr("for")).prop("checked", false);
        } else {
                $(this).addClass("zach");
                $(".fltrcheck" + $(this).attr("for")).prop("checked", true);
        }
    });
    $("body").on("click", ".fltronoff", function () {
        var rel=$(this).attr('rel');
        //console.log(".fltrcheck" + $(this).attr("for"));
        if ($(this).hasClass("zach")) {
                $(this).removeClass("zach");
                $(".fltrcheck" + $(this).attr("for")).prop("checked", false);
        } else {
                $('.fltronoff[rel^='+rel+']').removeClass("zach");
                $('.relcheck[rel^='+rel+']').prop("checked", false);;    
                $(this).addClass("zach");
                $(".fltrcheck" + $(this).attr("for")).prop("checked", true);
        }
    });
    
    $("body").on("click",".user-modal-open",function(){$('#usermodalwindow').modal('show')})
    
    $("body").on("click", ".radio", function () {
        var rel=$(this).attr('rel');
        
        //alert(rel);
        console.log(".fltrcheck" + $(this).attr("for"));
                $('.radio[rel^='+rel+']').removeClass("zach");
                $('.relradio[rel^='+rel+']').prop("checked", false);;    
                $('.relradio'+rel).prop("checked", false);;    
                $(this).addClass("zach");
                $(".fltrcheck" + $(this).attr("for")).prop("checked", true);
        
    });
    
    
    
    
    $("body").on("click", ".closefilteritem", function () {
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
            console.log(suggestion.data);
            if (!suggestion.data.house)
            {
                $("#useradesserror").html("Необходимо указать адрес до номера дома!").show();
                return false;
            }


            //return ;
            var dataString = JSON.stringify(suggestion);
            $("#geodatadadata").val(dataString);
            
            getLegalStores(dataString, '#useradesserror');
            //setUserAddrees();
            addUserAddrees(dataString,$("#useraddress").val());
            //
        }
    });
    
    
    $("#basketuseradress").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function (suggestion) {
            $("#adesserror").hide();
            console.log(suggestion.data);
            if (!suggestion.data.house)
            {
                $("#basketuseradresserror").html("Необходимо указать адрес до номера дома!").show();
                return false;
            }


            //return ;
            var dataString = JSON.stringify(suggestion);
            $("#geodatadadata").val(dataString);
            
            getLegalStores(dataString, '#basketuseradresserror');
            addUserAddrees(dataString, $("#basketuseradress").val());
            //addUserAddrees(dataString,$("#useraddress").val());
            //location = location.href;
            //
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

    $("#filtrform").on("change", " input", function () {
        sendfilterform();
    })

    $("body").on("click",".formsendbutton", function () {
        sendfilterform();
    })

    $("#sendajax2").click(function () {
        var dataString = $("#textarea").val();
        getLocalStores(dataString);
    })
    $("body").on("click", ".paybutton", function(){
        var product=$(this).attr("rel");
        showBasket(product );
        $("#bascetbottomblok").slideDown(); 
     })
        $("#bascetbottomblok").on("click", ".close", function(){ 
            $("#bascetbottomblok").slideUp(); 
        })
    
    $("#userAuthForm").submit(function () {
        var dataString = $("#userAuthForm").serialize();
        $.ajax({
            url: "/ajax/user-auth",
            cache: false,
            type: 'POST',
            dataType: 'json',
            data:dataString,
            success: function (data) {
                //console.log(data);
                if (data.error == false) {
                    //location = location.href;
                    getLegalStores($("#geodatadadata").text(), ".testlegalstor", true );
                    return false;
                } 
                if (data.phone){
                    if(data.isUser ) {
                        $('.olduser').removeClass("none");
                        $('.newuser').addClass("none"); 
                        $('#oldUser').html(data.username);//.show();
                        
                    }
                    else {
                        $('.olduser').addClass("none"); $('.newuser').removeClass("none");
                    }
                }
                $("#userAuthError").html(data.message);
                
                
            },
             error: function (xhr, ajaxOptions, thrownError) {
                $("#userAuthError").html("Ошибка соединения, попробуйте повторить попытку позже." + "<hr> " + xhr.status + " " + thrownError);
                
            }
         })
         return false;
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

    $(window).resize(function () {
        leftpanelclose();
    });
    $(".catalogshow").click(function () {
        $("#overcoverblack").fadeIn();
        $("#lefmobiletpanel").animate({left: "0"}, 500);

    })
    $("#lefmobiletpanelclose").click(function () {
        leftpanelclose()
    })

    $(".spoileropenlink").click(function () {
        var id = $(this).attr("rel");
        $("#spoiler-show-" + id).show();
        $("#spoiler-hide-" + id).hide();


        return false;
    })
    $(".favstar").click(function () {
        ($(this).hasClass("favon")) ? $(this).removeClass("favon") : $(this).addClass("favon");
        return false;
    })
    $(".favtext").click(function () {
        ($(this).hasClass("favon")) ? $(this).text("Убрать из избранного") :  $(this).text("Добавить в избранное");
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
function getLegalStores(dataString, obj = "#ajaxanswer2", wrelaoad=true) {
    $.ajax({
        //url: "/ajax/getstore",
        url: "/ajax-get-legal-store",
        type: 'POST',
        cache: false,
        data: {"value": dataString},
        success: function (data) {
            if (data == "200") {
                //console.log(data);
                $(".errorblock").hide();
                $("#searchpanel").stop().css({top: "-100px"});
                $("#uadress").show();
               if (wrelaoad) { 
                   location = location.href;
                   return false;
               }
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

function leftpanelclose() {
    $("#overcoverblack").fadeOut();
    $("#lefmobiletpanel").stop().animate({left: "-110%"}, 300);
}

function addUserAddrees(dadata=$("#geodatadadata").text(), address = $("#uadress span").text() ) {
    //var dadata = $("#geodatadadata").text();
     
    $.ajax({
        url: "/ajax-add-user-address",
        type: 'POST',
        data: {'dadata':dadata, "address":address},
        dataType: 'json',
        cache: false,
        success: function (html) {
           console.log(html);
           window.location.href = window.location.href; 
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
           //console.log(html.legalStore);
            $(".user_address_set").html(html.userAddress);
            //$(".testlegalstor").html("<h2>доступные магазины</h2></pre>" + print_r(html.legalStore) + "</pre>");//.css("border:1px soli red"); 
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

function show_scrollTop() {
    var wst = $(window).scrollTop();
    (wst > 500) ? $("#quicktop").stop().show() : $("#quicktop").stop().fadeOut()

}
function showBasket( productadd = 0 ){
    
    $.ajax({
            url: "/ajax/add-to-basket",
            cache: false,
            type: 'POST',
            //dataType: 'json',
            data:{"product" : productadd },
            success: function (data) {
                console.log(data);
                   $("#bascetbottomblok .content ").empty();
                if (data.products){ 
                    $.each(data.products, function(key, value) {
                //        console.log( value); 
                    //<div class='countitem' >"+ value.count +"</div>    
                    var basket = "<div class='blok both relative'><img class='imgicon iblok' src='/images/product/"+ value.image +"' ><span class='text'>" + value.name + "</span></div>";
                        
                        $("#bascetbottomblok .content ").append(basket);
                        
                        
                   })
                }
                
                $("#zakazcount").html(data.count); //data.total
                
                
            },
             error: function (xhr, ajaxOptions, thrownError) {
                $("#bascetbottomblok .content ").html("Ошибка соединения " + xhr.status + ", попробуйте повторить попытку позже." + "<hr> " + xhr.status + " " + thrownError);
                
            }
         })
         return false;
}

;
	