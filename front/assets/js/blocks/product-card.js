function zoomImg() {
  $('.product-card__big-img').trigger('zoom.destroy');
  let imgSrc = $('.product-card__big-img.active img').attr('src');
  $('.product-card__big-img.active').zoom({url: imgSrc});
}

$(document).ready(function(){
  zoomImg();
});

$(document).on('click','.product-card__small-img',function(){
  $('.product-card__small-img').removeClass('active');
  $(this).addClass('active');
  $('.product-card__big-img').removeClass('active');
  $('.product-card__big-img').eq($(this).index()).addClass('active');
  zoomImg();
});

$(document).on('click','.product-card__description-toggle',function(){
  $(this).parent().toggleClass('active');
});
