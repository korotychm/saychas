// function doSetCaretPosition (oField, iCaretPos) {
//
//      // IE Support
//      if (document.selection) {
//
//        // Set focus on the element
//        oField.focus ();
//
//        // Create empty selection range
//        var oSel = document.selection.createRange ();
//
//        // Move selection start and end to 0 position
//        oSel.moveStart ('character', -oField.value.length);
//
//        // Move selection start and end to desired position
//        oSel.moveStart ('character', iCaretPos);
//        oSel.moveEnd ('character', 0);
//        oSel.select ();
//      }
//
//      // Firefox support
//      else if (oField.selectionStart || oField.selectionStart == '0') {
//        oField.selectionStart = iCaretPos;
//        oField.selectionEnd = iCaretPos;
//        oField.focus ();
//      }
// }

$(document).ready(function () {
    showBasket(0);
    $(".overcover").delay(500).fadeOut("slow");
    $(".phoneinput").mask("+7 (999) 999-99-99");

    $(document).on('click','.phoneinput',function(){
      if ($(this).val() === '+7 (___) ___-__-__') {
        $(this).get(0).setSelectionRange(0, 0);
      }
    });

    window.onbeforeunload = function () {
        $(".overcover").stop().fadeIn();
    };
});

$(document).on('click', '.popup__close', function () {
    $(this).parent().parent().fadeOut();
});
