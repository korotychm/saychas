function setCustomSelectLabels(el) {
  let textValue = el.find('input:checked + span').text();
  el.find('.custom-select__label').text(textValue);
}

function setAllCustomSelects() {
  $('.custom-select--radio').each(function(){
    setCustomSelectLabels($(this));
  });
}

$(document).on('change','.custom-select--radio input',function(){
  let el = $(this).parent().parent().parent().parent();
  el.removeClass('active');
  setCustomSelectLabels(el);
});

$(document).on('click','.custom-select__label',function(){
  $('.custom-select__label').not(this).parent().removeClass('active');
  $(this).parent().toggleClass('active');
});

$(document).ready(function(){
  setAllCustomSelects();
});
