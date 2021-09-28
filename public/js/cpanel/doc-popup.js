$('#accept-doc-form').submit(function(e){
  e.preventDefault();
  if ($('#accept-doc').prop('checked')) {
    var msg = $('#accept-doc-form').serialize();
    $.ajax({
        url: "/",
        type: 'POST',
        cache: false,
        data: msg,
        success: function (data) {
          $('.popup--doc').fadeOut();
          setTimeout(function(){
            $('.popup--doc').remove();
          },500);
        }
    });
  }
})
