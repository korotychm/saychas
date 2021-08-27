$(document).on('click','.product-card__small-img',function(){
  $('.product-card__small-img').removeClass('active');
  $(this).addClass('active');
  $('.product-card__big-img').removeClass('active');
  $('.product-card__big-img').eq($(this).index()).addClass('active');
});

$(document).on('click','.product-card__description-toggle',function(){
  $(this).parent().toggleClass('active');
});

$(document).ready(function(){
  $('.testimonials__photos--carousel').slick(
    {
      infinite: false,
      slidesToShow: 10,
      slidesToScroll: 1
    }
  );
});
