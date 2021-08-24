$(document).ready(function () {
    showBasket(0);
    $(".overcover").delay(500).fadeOut("slow");
    $(".phoneinput").mask("+7 (999) 999-99-99");

    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };

});

$(document).on('click', '.popup__close', function () {
    $(this).parent().parent().fadeOut();
});

$(document).ready(function(){
  $('.select').niceSelect();
});
