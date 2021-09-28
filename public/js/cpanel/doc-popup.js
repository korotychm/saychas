$('#accept-doc-form').submit(function(e){
  e.preventDefault();
  if ($('#accept-doc').prop('checked')) {
    var msg = $('#accept-doc-form').serialize();
    $.ajax({
        url: "/control-panel/confirm-offer",
        type: 'POST',
        cache: false,
        data: msg,
        success: function (data) {
          console.log(data);
          $('.popup--doc').fadeOut();
        }
    });
  } else {
    $('.doc-to-accept__btns .checkbox__value').addClass('.checkbox__value--error');
  }
})
