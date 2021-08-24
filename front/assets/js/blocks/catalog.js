$(document).ready(function(){
  //Selects
  $('.select').niceSelect();
});

var filters2;

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
              console.log(data);
              filters2 = data;
              return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
             if (xhr.status !== 0) {
                    showAjaxErrorPopupWindow (xhr.status, thrownError);
                }
                return false;
            }
        });
    return false;
}

getCategoryFilters($("#testFiltersCategotyId").val());

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
