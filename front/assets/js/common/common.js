$.fn.setCursorPosition = function(pos) {
    if ($(this).get(0).setSelectionRange) {
      $(this).get(0).setSelectionRange(pos, pos);
    } else if ($(this).get(0).createTextRange) {
      var range = $(this).get(0).createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  };

$(document).ready(function () {
    showBasket(0);
    $(".overcover").fadeOut();
    $(".phoneinput").mask("+7 (999) 999-99-99");

    $(document).on('click','.phoneinput',function(){
      console.log('phone input focused');
      $(this).setCursorPosition(4);
    });

    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };
});

$(document).on('click', '.popup__close', function () {
    $(this).parent().parent().fadeOut();
});
