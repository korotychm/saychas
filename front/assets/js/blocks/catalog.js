$(document).ready(function(){
  //Selects
  $('.select').niceSelect();
});

function getCategoryFilters(categoryId){
      $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-category-filters",
            type: 'POST',
            cache: false,
            data: {"categoryId": categoryId},
            success: function (data) {
              showServicePopupWindow("Фильтры дла каталога",JSON.stringify(data));
              console.log('data:',data);
              var filters = data;
              var filter = new Vue({
                el: '#catalogfilter',
                data: filters
              });
              for (filter of filters.filters) {
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
              //Ranges
              $('.range').each(function(){
                setRange($(this));
              });
            },
            error: function (xhr, ajaxOptions, thrownError) {
             if (xhr.status !== 0) {
                    showAjaxErrorPopupWindow (xhr.status, thrownError);
                }
                return false;
            }
        });
}

$(document).ready(function(){

  if ($('#catalogfilter').length){
    getCategoryFilters(window.location.href.split("/").slice(-1)[0]);
  }

});

function sendfilterform() {
    //alert("!!!!");
    var dataString = $("#filtrform").serialize();
    $.ajax({
        beforeSend: function () {
            $("#overload").stop().show();

        },
        url: "/ajax-fltr",
        type: 'POST',
        cache: false,
        data: dataString,
        success: function (data) {
            $("#tovar-list").html(data);
            sendfilterformAndGetJson();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status != 0) {
                showAjaxErrorPopupWindow (xhr.status, thrownError);
            }
        }
    });
}

function sendfilterformAndGetJson() {
    //alert("!!!!");
    var dataString = $("#filtrform").serialize();
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
}

$(document).ready(function () {
    sendfilterform() ;
    $("#testProductButton").click(function(){
        sendfilterformAndGetJson();
    });

    $("#filtrform").on("change", " input", function () {
        sendfilterform();
    });

    $("body").on("click", ".formsendbutton", function () {
        sendfilterform();
    });

});
