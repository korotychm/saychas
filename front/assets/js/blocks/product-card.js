function zoomImg() {
  $('.product-card__big-img').trigger('zoom.destroy');
  let imgSrc = $('.product-card__big-img.active img').attr('src');
  $('.product-card__big-img.active').zoom({url: imgSrc});
}

$(document).ready(function(){

  zoomImg();

  function productImgSliderInit(){
    $('.product-card__img > div').slick({
        dots: true,
        arrows: false,
        infinite: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        mobileFirst: true,
        responsive: [
          {
            breakpoint: 768,
            settings: "unslick"
          }
        ]
    });
  }

  productImgSliderInit();

  $(window).resize(function() {
    if (!$('.product-card__img > div').hasClass('.slick-initialized') && $(window).width() < 768){
      productImgSliderInit();
    }
  });

  $('.testimonials__photos--carousel').slick(
    {
      infinite: false,
      slidesToShow: 5,
      slidesToScroll: 1,
      mobileFirst: true,
      responsive: [
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 10,
            slidesToScroll: 1
          }
        }
      ]
    }
  );

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
