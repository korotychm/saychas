function setTimepointText(loadinfo = false) {
    $.each($(".timepoint"), function (index, value) {
        var rel = $(this).attr("rel");
        var settext = $(this).find('.timepoint__option:checked').attr('rel');
        if (!settext){
          settext =  $(this).find('.timepoint__option').eq(1).attr('rel');
        }
        $("#" + rel).val(settext + ", ");
        console.log($("#" + rel).val());
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
});
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
    $(".cart__store .checkbox input").prop('checked',$(this).prop('checked')).change();
    calculateBasketHeader();
    loadPayInfo();
});

// Чекбокс товары магазина
$(document).on('click','.cart__store-top .checkbox input',function(){
    let store = $(this).data('provider');
    $('.cart__product .checkbox input[data-provider="' + store + '"]').prop('checked',$(this).prop('checked')).change();
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

  if ($('.cart__product .checkbox input[data-provider="'+store+'"]:checked').length){
    // Самовывоз доступен
    $('#providerblok-' + store).find('.cart__store-self-delivery').removeClass('disabled');
  } else {
    // Самовывоз недоступен
    $('#providerblok-' + store).find('.cart__store-self-delivery').addClass('disabled');
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

function orderPayTinkoff(orderId)
{
    var apiUrl = "/tinkoff/payment/" + orderId

    $.ajax({
        beforeSend: function () {
            showServicePopupWindow("Оплата заказа", "Готовим к оплате заказ " + orderId, "", true);
        },
        url: apiUrl,
        cache: false,
        //type: 'POST',
        //dataType: 'json',
        //data: {"product": productId, "count": count},
        success: function (data) {
            if(data.result) {
                location = data.answer.PaymentURL;
            }
            else {
                showServicePopupWindow("Ошибка", "<pre>" + JSON.stringify(data, true, 2) + "</pre>", "", true);
            }

        },
        error: function (xhr, ajaxOptions, thrownError) {
            showAjaxErrorPopupWindow(xhr.status, thrownError);
        }
    });
}

// Самовывоз из магазина
$(document).on('change','.cart__store-self-delivery input',function(){
    //let providerId = $(this).data('provider');
    calculateBasketMerge($("#user-basket-form").serialize(), true);
});


function calculateSelfDelevery()
{
    let selftDeliveryCount = $(".cart__store-self-delivery:not(.disabled) input:checked").length,
        selftDeliveryProducts = $(".cart__store-self-delivery:not(.disabled) input:checked").parents().eq(3).find('.checkbox input:checked').length;

    if (selftDeliveryCount > 0) {
        $("#selfdeleverymainblok").show();
        $('.cart-self-delivery__store').hide();
        $(".cart__store-self-delivery:not(.disabled) input:checked").each(function(){
          let store = $(this).parents().eq(3);
          $('.cart-self-delivery__store').eq(store.index()).show();
        });
    } else {
        $("#selfdeleverymainblok").hide();
    }

    $("#selfdeleverycountproduct").html(selftDeliveryProducts);
    $("#selfdeleverycountstore").html(selftDeliveryCount);
}

//Способы доставки
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
            setAllCustomSelects();
            setTimepointText();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#basketordermerge").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
            $("#basket-ordermerge-cover").hide();
            return false;
        }
    });
}
//Способы оплаты
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
            $('#baskepaycardinfo .select').niceSelect();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#baskepaycardinfo").html("<span class='iblok contentpadding'>Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "</span>");
            return false;
        }
    });
}
// Изменения в корзине за время отсутствия
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

const clockTemplate = `<div class='waiting'>
                        <svg width="73px" height="88px" viewBox="0 0 73 88" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <g id="hourglass">
                          <path d="M63.8761664,86 C63.9491436,84.74063 64,83.4707791 64,82.1818182 C64,65.2090455 57.5148507,50.6237818 48.20041,44 C57.5148507,37.3762182 64,22.7909545 64,5.81818182 C64,4.52922091 63.9491436,3.25937 63.8761664,2 L10.1238336,2 C10.0508564,3.25937 10,4.52922091 10,5.81818182 C10,22.7909545 16.4851493,37.3762182 25.79959,44 C16.4851493,50.6237818 10,65.2090455 10,82.1818182 C10,83.4707791 10.0508564,84.74063 10.1238336,86 L63.8761664,86 Z" id="glass" fill="#f2f2f2"></path>
                          <rect id="top-plate" fill="#252525" x="0" y="0" width="74" height="8" rx="2"></rect>
                          <rect id="bottom-plate" fill="#252525" x="0" y="80" width="74" height="8" rx="2"></rect>
                          <g id="top-sand" transform="translate(18, 21)">
                          <clipPath id="top-clip-path" fill="white">
                          <rect x="0" y="0" width="38" height="21"></rect>
                          </clipPath>
                          <path fill="#ff4b2d" clip-path="url(#top-clip-path)" d="M38,0 C36.218769,7.51704545 24.818769,21 19,21 C13.418769,21 1.9,7.63636364 0,0 L38,0 Z"></path>
                          </g>
                          <g id="bottom-sand" transform="translate(18, 55)">
                          <clipPath id="bottom-clip-path" fill="white">
                          <rect x="0" y="0" width="38" height="21"></rect>
                          </clipPath>
                          <g clip-path="url(#bottom-clip-path)">
                          <path fill="#ff4b2d" d="M0,21 L38,21 C36.1,13.3636364 24.581231,0 19,0 C13.181231,0 1.781231,13.4829545 0,21 Z"></path>
                          </g>
                          </g>
                          </g>
                        </svg>
                      </div>`;

function waitingOrderStatusVerification(orderId, idInterval = false) {
    var date = new Date();
    status = 5;
    $.ajax({
        beforeSend: function () {
        },
        url: "/ajax-check-order-status",
        type: 'POST',
        cache: false,
        data: {"orderId": orderId},
        success: function (data) {
            if (data.order_status == 0) {
                //showServicePopupWindow("Готовим заказ к оплате", "<b>Текущий статус: " + data.order_status + "</b><hr>" + date + "<pre>" + JSON.stringify(data, true, 2) + "</pre>", "", true);
                if ($('#ServiceModalWindow .popup__heading').text() != 'Готовим заказ к оплате...'){
                  showServicePopupWindow("Готовим заказ к оплате...", clockTemplate, "", true);
                }
            } else {
                //showServicePopupWindow("Заказ полностью сформирован", "переходим на страницу оплаты", '<button class="changed-products__btn formsendbutton" onclick="orderPayTinkoff(\'' + orderId +'\')">Оплатить заказ</div>');
                if (idInterval != false) {
                  clearInterval(idInterval);
                }
                orderPayTinkoff(orderId);
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
            showServicePopupWindow("Отправляем заказ...", clockTemplate, "", true);
        },
        url: "/send-basket-data",
        type: 'POST',
        cache: false,
        data: $("#user-basket-form").serialize(),
        success: function (data) {
            //console.log(data)
            //showServicePopupWindow("Формируем заказ...", JSON.stringify(data));
            showServicePopupWindow("Формируем заказ...", clockTemplate, "", true);
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
            //$("#basket-payinfo-cover").hide();
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
//        token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
//        type: "ADDRESS",
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


    $(document).on('change', '#basketordermerge .custom-select__option input', function () {
        calculateBasketMerge($("#user-basket-form").serialize(), true);
    });

    calculateBasketMerge($("#user-basket-form").serialize(), true);

    setAllCustomSelects();

    $("body").on("click", "#sendbasketbutton", function () {
        checkBasketDataBeforeSend();
    });

    $(document).on('click', '.cart__radio', function () {
        $(this).prev().prop('checked',true).change();
        setTimepointText();
    });

    $(document).on('change', '.cart__radio-group > input', function () {
        console.log('123');
        loadPayInfo();
    });

});
