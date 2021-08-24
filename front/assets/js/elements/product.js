$(document).on('click', '.paybutton', function () {
    var product = $(this).attr("rel");
    showBasket(product);
    $("#bascetbottomblok").slideDown();
});
