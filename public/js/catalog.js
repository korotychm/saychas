$(document).ready(function(){

  if ($('#catalog-wrap').length){

    var catalog = new Vue({
      el: '#catalog-wrap',
      data: {
        category_id: '',
        rangeprice: {},
        filters: [],
        products: [],
        filterUpdated: false
      },
      methods: {
        setRangesValues() {
          this.rangeprice.currentMin = this.rangeprice.minprice / 100;
          this.rangeprice.currentMax = this.rangeprice.maxprice / 100;

          for (let filter of this.filters) {
            if (filter.type == 2){
              let min = filter.options.reduce(function(prev, curr) {
                return +prev.value < +curr.value ? prev : curr;
              }).value;
              let max = filter.options.reduce(function(prev, curr) {
                return +prev.value > +curr.value ? prev : curr;
              }).value;

              let diff = max - min;
              let float = false;

              for (option of filter.options){
                if (isFloat(+option.value)){
                  float = true;
                  break;
                }
              }

              let step = 1;
              if (diff > 10000) {
                step = 100;
              }
              else if (diff > 1000) {
                step = 10;
              }
              else if (diff < 10 && float) {
                step = 0.1;
              }

              if (!float && diff % step != 0){
                max = +max + step;
              }
              filter.min = min;
              filter.max = max;
              filter.currentMin = min;
              filter.currentMax = max;
              filter.step = step;
              filterUpdated: false
            }
          }
          this.filterUpdated = true;
        },
        getProducts() {
          let formData = $("#filter-form").serialize();
          console.log(formData),
          axios
            .post('/ajax-fltr-json',formData)
            .then(response => {
              this.filterUpdated = false;
              this.products = response.data.products;
              console.log(response.data);
            });
        }
      },
      created() {
          this.category_id = window.location.href.split("/").slice(-1)[0],
          axios
            .post('/ajax-get-category-filters',
              Qs.stringify({
                categoryId : this.category_id,
                test : this.category_id
              }))
            .then(response => (
              this.category_id = response.data.category_id,
              this.rangeprice = response.data.rangeprice,
              this.filters = response.data.filters,
              this.setRangesValues()
            ));
      },
      updated() {
        if (this.filterUpdated) {
          this.getProducts()
        }
      }
    });

  }

});

function isFloat(n) {
    return n === +n && n !== (n|0);
}

$(document).on('input','.range input[type="range"]',function(){
  setRange($(this).parent());
});

$(document).on('change','.range input[type="range"]',function(){

  let el = $(this).parent(),
      minVal = +el.find('.range__left').val(),
      maxVal = +el.find('.range__right').val(),
      hidden = el.find('.range__hidden');

  if (el.hasClass('range--price')){
    hidden.val((minVal * 100) + ';' + (maxVal * 100));
  } else {
    hidden.val(minVal + ';' + maxVal);
  }
  $('#test-filter-button').trigger('click');
});

$(document).on('change','.tooltip-from',function(){
  let range = $(this).parent().parent().parent().find('.range__left');
  if ($(this).val() != range.val()){
    range.val($(this).val()).change();
  }
  setRange(range.parent());
});

$(document).on('change','.tooltip-to',function(){
  let range = $(this).parent().parent().parent().find('.range__right');
  if ($(this).val() != range.val()){
    range.val($(this).val()).change();
  }
  setRange(range.parent());
});

function setRange(el) {

    let min = +el.find('.range__left').attr('min'),
        max = +el.find('.range__right').attr('max'),
        minVal = +el.find('.range__left').val(),
        maxVal = +el.find('.range__right').val();

    if (minVal < min){
      minVal = min;
      el.find('.range__left').val(minVal);
    }
    if (maxVal > max){
      maxVal = max;
      el.find('.range__right').val(maxVal);
    }

    if (minVal > maxVal){
      minVal = maxVal;
      el.find('.range__left').val(minVal);
      let tmp = maxVal;
      maxVal = minVal;
      minVal = tmp;
      el.find('.range__left').val(minVal);
      el.find('.range__right').val(maxVal);
    }

    let leftPos =  (minVal - min) / (max - min) * 100 + '%',
        rightPos = 100 - (maxVal - min) / (max - min) * 100 + '%';

    el.find('.range__range, .range__thumb--left').css('left', leftPos);
    el.find('.range__range, .range__thumb--right').css('right', rightPos);

    if (minVal != el.find('.tooltip-from').val()){
      el.find('.tooltip-from').val(minVal);
    }
    if (maxVal != el.find('.tooltip-to').val()){
      el.find('.tooltip-to').val(maxVal);
    }
    return;
}


// Range end

$(document).on('click', '.filter__show-all', function(e){
  e.preventDefault();
  $(this).parent().find('.filter__scroll').toggleClass('active');
  $(this).parent().find('.checkbox').removeClass('hidden');
  $(this).parent().find('.filter__search input').val('');
});

jQuery.expr[':'].contains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};

$(document).on('keyup', '.filter__search input', function(e){
  let val = $(this).val();
  if (val!=''){
    $(this).parent().parent().parent().find('.checkbox').addClass('hidden');
    $(this).parent().parent().parent().find('.checkbox:contains(' + val + ')').removeClass('hidden');
  } else {
    $(this).parent().parent().parent().find('.checkbox').removeClass('hidden');
  }
});
