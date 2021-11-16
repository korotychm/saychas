$(document).on('click','.cart__mobile-radio-group label',function(){
  $(this).parent().find('label').removeClass('active');
  $(this).addClass('active');
});
