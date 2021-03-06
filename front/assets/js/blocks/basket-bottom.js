function showBasket(productadd = 0) {
    $.ajax({
        url: "/ajax/add-to-basket",
        cache: false,
        type: 'POST',
        //dataType: 'json',
        data: {"product": productadd},
        success: function (data) {
            //console.log(data);
            $("#basketbottom .basketbottom__content").empty();
            if (data.products) {
                $.each(data.products, function (key, value) {
                    var basket = "<div class='basketbottom__item'><div class='basketbottom__img'><img src='/images/product/" + value.image + "' ></div><div class='basketbottom__text'>" + value.name + "</div></div>";
                    $("#basketbottom .basketbottom__content").append(basket);
                });
            }
            $("#zakazcount").html(data.count); //data.total
            console.log(data.count);
            if (data.count == 0){
              $("#zakazcount").hide();
            } else {
              $("#zakazcount").show();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#basketbottom .basketbottom__content").html("Ошибка соединения " + xhr.status + ", попробуйте повторить попытку позже." + "<hr> " + xhr.status + " " + thrownError);
        }
    });
    return false;
}

$(document).on("click", ".basketbottom__close", function () {
    $("#basketbottom").removeClass('active');
});
