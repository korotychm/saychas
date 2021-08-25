$(document).ready(function(){

  if ($('#catalogfilter').length){

    //getCategoryFilters(window.location.href.split("/").slice(-1)[0]);

    var catalog = new Vue({
      el: '#catalogfilter',
      data: {
        category_id: '',
        rangeprice: {},
        filters: []
      },
      methods: {
        setRangesValues() {
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
              filter.step = step;
            }
          }
        },
        sendfilterformAndGetJson() {
            //alert("!!!!");
            var dataString = $("#filter-form").serialize();
            console.log(dataString);
            $.ajax({
                beforeSend: function () {
                    $("#overload").stop().show();
                },
                url: "/ajax-fltr-json",
                type: 'POST',
                cache: false,
                data: dataString,
                success: function (data) {
                    $("#tovar-list-json").html(JSON.stringify(data,true,2));
                    return false;
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if (xhr.status != 0) {
                        showAjaxErrorPopupWindow (xhr.status, thrownError);
                    }
                }
            });
            return false;
        },
        getProducts() {
          let formData = $("#filter-form").serialize();
          axios
            .post('/ajax-fltr-json',formData)
            .then(response => (
              console.log(response),
              $("#tovar-list-json").html(JSON.stringify(response,true,2))
            ));
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
      mounted() {

      },
      updated() {
        this.$nextTick(() => {
          this.getProducts();
        })
      }
    });

  }

});


$(document).ready(function () {

    // $("#testProductButton").click(function(){
    //     sendfilterformAndGetJson();
    // });
    //
    // $("#filter-form").on("change", "input", function () {
    //     sendfilterformAndGetJson();
    // });

});
