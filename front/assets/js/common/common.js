$(".phoneinput").mask("+7 (999) 999-99-99");

$(document).on('click', '.popup__close', function () {
    $(this).parent().parent().fadeOut();
});
