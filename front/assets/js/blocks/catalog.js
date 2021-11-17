$('.catalog__filter-trigger').click(function(){
  $('.filter').addClass('active');
});
$('.filter__close,.filter__close-btn').click(function(){
  $('.filter').removeClass('active');
});
