$(document).ready(function(){
  $('.products-carousel--4').slick(
    {
      infinite: true,
      slidesToShow: 4,
      slidesToScroll: 4,
      responsive: [
        {
          breakpoint: 1267,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2
          }
        }
      ]
    }
  );
  $('.products-carousel--3').slick(
    {
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 3,
      responsive: [
        {
          breakpoint: 767,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2
          }
        }
      ]
    }
  );
});
