$(document).on('click','.cart__mobile-radio-group label',function(){
  $(this).parent().find('label').removeClass('active');
  $(this).addClass('active');
});

$(document).on('click','.panel__trigger',function(){
  $(this).toggleClass('active');
  $(this).parent().toggleClass('active');
});
