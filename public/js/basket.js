function setTimepointText(loadinfo = false) {
    $.each($(".timepoint"), function (index, value) {
        var rel = $(this).attr("rel");
        var settext = $('option:selected', this).attr('rel');
        $("#" + rel).val(settext + ", ");
    });
    if (loadinfo)
        loadPayInfo();
}

// Изменение количества товара в корзине
$(document).on('click','.cart__product-quantity button',function(e){
  e.preventDefault();
  $(this).parent().find('button').removeClass('disabled');
  let input = $(this).parent().find('input'),
      newVal = +input.val() + +$(this).data('step');
  if (newVal == 1 || newVal == input.attr('max')){
    $(this).addClass('disabled');
  }
  input.val(newVal);
  let productId = input.data('product');
  $('.cart__checkbox input[data-product="' + productId + '"]').val(input.val());
  calculateBasketItem(productId);
  loadPayInfo();
})
// Подсчет количества товаров в корзине
function calculateProductTotal() {
    let total = 0;
    $(".cart__product .checkbox input:checked").each(function() {
          let productId = $(this).data('product'),
              quantityInput = $('#countproduct-' + productId);
          total += +quantityInput.val();
    });
    return total;
}
// Заголовок страницы корзины
function calculateBasketHeader(productId)
{
    let totalStores = 0, totalProducts = 0;
    $(".cart__store").each(function() {
      let storeProducts = $(this).find(".cart__product .checkbox input:checked").length;
      totalProducts += storeProducts;
      if (storeProducts) totalStores++;
    });
    // $.each($(".basketproviderblok"), function (index, value) {
    //     var id = $(this).attr("id"), rel = $(this).attr("rel");
    //     var products = $("#" + id + " .checkallprovider.zach ").length;
    //
    //     if ($("#" + id + " .checkallprovider.zach ").length === $("#" + id + " .checkallprovider").length) {
    //         $("#checkallallprovider-" + rel).addClass("zach");
    //     }
    //
    //     if ($(".checkprovider.zach ").length === $(".checkprovider").length) {
    //         $("#checkallavailble").addClass("zach");
    //     }
    //
    //     if (products > 0) {
    //         totalshops++;
    //         totalproduct += products;
    //         $("#" + id + " .basketrowselfdelevery").show();
    //     } else {
    //         $("#selfdeleveryonoff-" + rel).removeClass("zach");
    //         $("#" + id + " .basketrowselfdelevery").hide();
    //
    //         $("#selfdeleverycheckbox-" + rel).prop("checked", false);
    //         $("#providerblok-" + rel).removeClass("goself");
    //         $("#provider_addressappend" + rel).hide();
    //         $("#seldeleveryblokrow-" + rel).removeClass('seldeleveryblokrowcountme').hide();
    //     }
    // });

    let h1 = "Товары не выбраны";
    if (totalProducts){
      let productUnit = 'позиций',
          storeUnit = 'магазина',
          lastNumber = totalProducts.toString().split('').pop();

      if (lastNumber == 1) productUnit = 'позиция';
      else if (lastNumber > 1 && lastNumber < 5 && (totalProducts < 10 || totalProducts > 20)) productUnit = 'позиции';

      if (totalStores > 1) storeUnit = 'магазинов';

      let countTotalProducts = calculateProductTotal();

      h1 = `${totalProducts} ${productUnit} из ${totalStores} ${storeUnit} <span>(Всего товаров: ${countTotalProducts})</span>`;
    }
    $("#h1title").html(h1);
}
//Чекбокс все товары
$(document).on('click','#checkallavailble input',function(){
    console.log('all',$(this).prop('checked'));
    $(".cart__store .checkbox input").prop('checked',$(this).prop('checked')).change();
    //$(".selfdeleveryallall").removeClass("selfdeleverycountme").hide();
    calculateBasketHeader();
    loadPayInfo();
});
// Чекбокс товары магазина
$(document).on('click','.cart__store-top .checkbox input',function(){
    let store = $(this).data('provider');
    $('.cart__product .checkbox input[data-provider="'+store+'"]').prop('checked',$(this).prop('checked')).change();
    //$(".selfdeleveryproduct-" + rel).removeClass("selfdeleverycountme").hide();
    calculateBasketHeader();
    loadPayInfo();
});
$(document).on('change','.cart__product .checkbox input',function(){
  let store = $(this).data('provider');
  $('.cart__store-top .checkbox input[data-provider="'+store+'"]').prop('checked',true);
  // Отмечаем или снимаем чекбокс магазина
  if ($('.cart__product .checkbox input[data-provider="'+store+'"]').length == $('.cart__product .checkbox input[data-provider="'+store+'"]:checked').length){
    $('.cart__store-top .checkbox input[data-provider="'+store+'"]').prop('checked',true);
  } else {
      $('.cart__store-top .checkbox input[data-provider="'+store+'"]').prop('checked',false);
  }
  // Отмечаем или снимаем чекбокс всех товаров
  if ($('.cart__product .checkbox input').length == $('.cart__product .checkbox input:checked').length){
    $('#checkallavailble input').prop('checked',true);
  } else {
    $('#checkallavailble input').prop('checked',false);
  }
  // Отмечаем или снимаем чекбоксы для whathappened
  $(this).parent().parent().find('input').prop('checked',$(this).prop('checked'));
  calculateBasketHeader();
  loadPayInfo();
});

// Удалить продукт
$(document).on('click','.cart__product .cart__product-del',function(){
    var productId = $(this).data('product');
    var providerId = $(this).data('provider');
    $.ajax({
        beforeSend: function () {
        },
        url: "/ajax/del-from-basket",
        type: 'POST',
        cache: false,
        data: {'productId': productId},
        success: function (data) {
            $("#basketrow-" + productId).remove();
            if (!$("#providerblok-" + providerId + " .cart__product").length){
              $("#providerblok-" + providerId).remove();
            }
            calculateBasketHeader();
            loadPayInfo();
            showBasket(0);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status !== 0) {
                showAjaxErrorPopupWindow(xhr.status, thrownError);
            }
        }
    });
});
//Посчет суммы товаров одной позиции
function calculateBasketItem(productId)
{
    let count = $("#countproduct-" + productId).val();
    $.ajax({
        url: "/ajax/calculate-basket-item",
        cache: false,
        type: 'POST',
        dataType: 'json',
        data: {"product": productId, "count": count},
        success: function (data) {
            $("#priceproduct-" + productId).html(data.totalFomated);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Ошибка соединения " + xhr.status + "" + "\r\n " + xhr.status + " " + thrownError);
        }
    });
}


//
// $(".checkallallprovider").click(function () {
//     $("#checkallavailble").removeClass("zach");
//     var rel = $(this).attr('rel');
//     if ($(this).hasClass("zach")) {
//         $(this).removeClass("zach");
//         $(".provider-" + rel).removeClass("zach");
//         $(".povidercheck-" + rel).prop("checked", false);
//         $(".selfdeleveryallprovider-" + rel).removeClass("selfdeleverycountme").hide();
//     } else {
//         $(this).addClass("zach");
//         $(".provider-" + rel).addClass("zach");
//         $(".povidercheck-" + rel).prop("checked", true);
//         $(".selfdeleveryallprovider-" + rel).addClass("selfdeleverycountme").show();
//     }
//     calculateBasketHeader();
//     loadPayInfo();
// });
//
// $(".checkallprovider").click(function () {
//
//     var rel = $(this).attr('rel');
//     var provider = $(this).attr('provider');
//     $("#checkallavailble").removeClass("zach");
//     $("#checkallallprovider-" + provider).removeClass("zach");
//     if ($(this).hasClass("zach")) {
//         $(this).removeClass("zach");
//         $(".product-" + rel).prop("checked", false);
//         $(".selfdeleveryproduct-" + rel).removeClass("selfdeleverycountme").hide();
//
//     } else {
//         $(this).addClass("zach");
//         $(".product-" + rel).prop("checked", true);
//         $(".selfdeleveryproduct-" + rel).addClass("selfdeleverycountme").show();
//     }
//     calculateBasketHeader();
//     loadPayInfo();
// });

// Самовывоз из магазина
$(document).on('change','.cart__store-self-delivery input',function(){
    //let providerId = $(this).data('provider');
    calculateBasketMerge($("#user-basket-form").serialize(), true);
});

function calculateSelfDelevery()
{
    var store = $(".seldeleveryblokrowcountme").length;
    var product = $(".seldeleveryblokrowcountme .selfdeleverycountme").length;
    //console.log();
    if (store > 0) {
        $("#selfdeleverymainblok").show();
    } else {
        $("#selfdeleverymainblok").hide();
    }
    $("#selfdeleverycountproduct").html(product);
    $("#selfdeleverycountstore").html(store);
}


function calculateBasketMerge(dataString, loadinfo = false)
{
    $.ajax({
        beforeSend: function () {
            $("#basket-ordermerge-cover").stop().fadeIn();
            setTimepointText(loadinfo);
        },
        url: "/ajax-basket-order-merge",
        type: 'POST',
        cache: false,
        data: dataString,
        success: function (data) {
            $("#basketordermerge").html(data);
            $("#basket-ordermerge-cover").hide();
            setTimepointText();
            $('#basketordermerge .select').niceSelect();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#basketordermerge").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
            $("#basket-ordermerge-cover").hide();
            return false;
        }
    });

}

function calculateBasketPayCard()
{
    var dataString = $("#user-basket-form").serialize();
    $.ajax({
        url: "/ajax-basket-pay-card-info",
        type: 'POST',
        cache: false,
        data: dataString,
        success: function (data) {
            $("#baskepaycardinfo").html(data);
            $('.select').niceSelect();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#baskepaycardinfo").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
            return false;
        }
    });

}

function whatHappened(noclose = false) {
    $.ajax({
        beforeSend: function () {
        },
        url: "/ajax-basket-changed",
        type: 'POST',
        cache: false,
        success: function (data) {
            if (data.result) {
                $("#ServiceModalWindow .modal-title").html("Изменения в товарах");

                $("#ServiceModalWindow #ServiceModalWraper").html('<p class="changed-products__subtitle">Пока вас не было, произошли следующие изменения в товарах:</p><ul class="changed-products"></ul>');

                for (var productId in data.products) {

                    var product = data.products[productId];

                    var productHtml = '<li class="changed-products__item">';

                    var imgSrc = $('#basketrow-' + productId).find('.imageproduct img').attr('src');
                    var title = $('#basketrow-' + productId).find('.titleproduct').text();

                    productHtml += '<div class="changed-products__img"><img src="' + imgSrc + '" alt=""></div>';
                    productHtml += '<div class="changed-products__title">' + title + '</div>';

                    if (product.rest == 0) {
                        //вывод "товар закончился"
                        productHtml += '<div class="changed-products__status"><div class="changed-products__na">Товар закончился</div></div>';
                    } else {
                        productHtml += '<div class="changed-products__status"><table>';
                        if (product.oldprice) {
                            //вывод измененной цены
                            productHtml += '<tr>';
                            productHtml += ('<td class="changed-products__from">' + (parseInt(product.oldprice) / 100).toLocaleString() + ' ₽</td>');
                            productHtml += ('<td class="changed-products__to">' + (product.price / 100).toLocaleString() + ' ₽</td>');
                            productHtml += '</tr>';
                        }
                        if (product.oldrest) {
                            //вывод измененных остатков
                            productHtml += '<tr>';
                            productHtml += ('<td class="changed-products__from">' + product.oldrest + ' шт.</td>');
                            productHtml += ('<td class="changed-products__to">' + product.rest + ' шт.</td>');
                            productHtml += '</tr>';
                        }
                        productHtml += '</table></div>';
                    }
                    productHtml += '</li>';
                    $('#ServiceModalWindow .changed-products').append(productHtml);
                }

                //Магазины

                for (var storeId in data.stores) {

                    var logoSrc = $('#providerblok-' + storeId).find('.brandlogo img').attr('src');

                    var storeHtml = '<li class="changed-products__item changed-products__item--store">';
                    storeHtml += '<div class="changed-products__store">';
                    storeHtml += '<div class="changed-products__store-logo"><img src="' + logoSrc + '" alt=""></div>';
                    storeHtml += '<div class="changed-products__store-items">';
                    for (var productId of data.stores[storeId]) {
                        var imgSrc = $('#basketrow-' + productId).find('.imageproduct img').attr('src');
                        storeHtml += '<div class="changed-products__store-item"><img src="' + imgSrc + '" alt=""></div>';
                    }
                    storeHtml += '</div>';
                    storeHtml += '</div>';
                    storeHtml += '<div class="changed-products__status"><div class="changed-products__na">Магазин закрыт</div></div>';
                    storeHtml += '</li>';
                    $('#ServiceModalWindow .changed-products').append(storeHtml);
                }

                if (noclose) {
                    $("#ServiceModalWindow .close").remove();
                    $("#ServiceModalWindow .modal-footer").html('<button class="changed-products__btn formsendbutton" onclick="location.reload()">Буду иметь в виду</div>');
                } else {
                    $("#ServiceModalWindow .modal-footer").html('<button class="changed-products__btn formsendbutton" onclick="$(`#ServiceModalWindow`).modal(`hide`)">Буду иметь в виду</div>');
                }

                $("#ServiceModalWindow").modal("show");

            }
            return false;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status !== 0) {
                showAjaxErrorPopupWindow(xhr.status, thrownError);
            }
        }
    });
}

function waitingOrderStatusVerification(orderId, idInterval = false) {
    var date = new Date();
    status = 5;
    $.ajax({
        beforeSend: function () {
        },
        url: "/ajax-chek-order-status",
        type: 'POST',
        cache: false,
        data: {"orderId": orderId},
        success: function (data) {
            if (data.order_status == 0) {
                showServicePopupWindow("Ожидаем изменения статуса", "<b>Текущий статус: " + data.order_status + "</b><hr>" + date + "<pre>" + JSON.stringify(data, true, 2) + "</pre>", "", true);

            } else {
                showServicePopupWindow("Заказ полностью сформирован", "переходим на страницу оплаты", '<button class="changed-products__btn formsendbutton" onclick="return false;">Оплатить заказ</div>');
                if (idInterval != false) {
                    clearInterval(idInterval);
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status !== 0) {
                showAjaxErrorPopupWindow(xhr.status, thrownError);
            }
           // status = false;
        }
    });
    return;//status;
}

function checkBasketDataBeforeSend() {
    //var dataString = $("#user-basket-form").serialize();
    $.ajax({
        beforeSend: function () {
        },
        url: "/ajax-basket-check-before-send",
        type: 'POST',
        cache: false,
        data: $("#user-basket-form").serialize(),
        success: function (data) {
            if (data.result) {
                sendBasketData();
            } else {
                if (data.reload !== null) {
                    location.href = data.reloadUrl;
                    return false;
                }
                whatHappened(true);
            }
            return false;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status !== 0) {
                showAjaxErrorPopupWindow(xhr.status, thrownError);
            }
            return false;
        }
    });
    return false;
}

function sendBasketData() {
    $.ajax({
        beforeSend: function () {
            showServicePopupWindow("Отправка данных о заказе", "....");
        },
        url: "/send-basket-data",
        type: 'POST',
        cache: false,
        data: $("#user-basket-form").serialize(),
        success: function (data) {
            //console.log(data)
            showServicePopupWindow("Формируем заказ", JSON.stringify(data));
            if (data.result == true) {
                //    location = "/client-orders";
                //console.log(data);
                intervalWaitingOrderStatus = setInterval(
                        function () {
                            var result = waitingOrderStatusVerification(data.orderId, intervalWaitingOrderStatus);
                           // console.log(result);
                           },
                        500);

                //   return false;
            }
            if (data.result == false) {
                showServicePopupWindow("Ошибка", JSON.stringify(data.description));
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status !== 0) {
                showAjaxErrorPopupWindow(xhr.status, thrownError);
            }
        }
    });
    //$("#ServiceModalWindow #ServiceModalWraper").html("+ + + +");
    //$("#ServiceModalWindow").modal("show");
}

function loadPayInfo() {
    var dataString = $("#user-basket-form").serialize();
    $.ajax({
        beforeSend: function () {
            $("#basket-payinfo-cover").stop().fadeIn();
            calculateBasketMerge(dataString);
            calculateBasketPayCard();
            calculateBasketHeader();
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


$(function () {
    whatHappened();
    loadPayInfo();

    $("#basketuseradress").suggestions({
        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
        type: "ADDRESS",
        onSelect: function (suggestion) {
            $("#basketuseradresserror").hide();
            if (!suggestion.data.house)
            {
                $("#basketuseradresserror").html("Необходимо указать адрес до номера дома!").show();
                return false;
            }
            var dataString = JSON.stringify(suggestion);
            $("#geodatadadata").val(dataString);

            getLegalStores(dataString, '#basketuseradresserror');
            addUserAddrees(dataString, $("#basketuseradress").val());

        }
    });

    $("#testbeforesend").click(function () {
        checkBasketDataBeforeSend();
    });

    /**/
    $("body").on("change", ".timepoint", function () {
        calculateBasketMerge($("#user-basket-form").serialize(), true);
    });/**/

    calculateBasketMerge($("#user-basket-form").serialize(), true);

    $("body").on("click", "#sendbasketbutton", function () {
        checkBasketDataBeforeSend();
    });


    $(document).on('change', '.cart__radio-group input', function () {
        console.log('radio');
        loadPayInfo();
    });

});
