function addUserAddrees(dadata = $("#geodatadadata").text(), address = $("#uadress span").text()) {
    $.ajax({
        url: "/ajax-add-user-address",
        type: 'POST',
        data: {'dadata': dadata, "address": address},
        dataType: 'json',
        cache: false,
        success: function (data) {
            location = location.href;
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

function setUserAddrees() {
    var dadata = $("#geodatadadata").text();
    var address = $("#uadress").text();

    $.ajax({
        url: "/ajax-set-user-address",
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function (html) {
            $(".user_address_set").html(html.userAddress);
            return true;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
            return true;
        }
    });
}

$(document).ready(function(){

  $(".deleteuseraddress").click(function () {
      var rel = $(this).attr("rel");
      $.ajax({
          beforeSend: function () {},
          url: "/user-delete-address",
          type: 'POST',
          cache: false,
          data: {'dataId': rel, 'reload': $(this).attr("data-reload")},
          success: function (data) {
              //if(data.result == 1)
              $("#useradress-" + rel).fadeOut();
          },
          error: function (xhr, ajaxOptions, thrownError) {
              showAjaxErrorPopupWindow (xhr.status, thrownError);
              return false;
          }
      });
  });

  $(".setuseraddress").click(function (e) {
      e.stopPropagation();
      console.log('clicked');
      var rel = $(this).attr("rel");
      $.ajax({
          beforeSend: function () {},
          url: "/user-set-default-address",
          type: 'POST',
          cache: false,
          data: {'dataId': rel, 'reload': $(this).attr("data-reload")},
          success: function (data) {
              //console.log(data);
              //if(data.result == 1)
              //    $("#useradress-" + rel ).fadeOut();
              location = location.href;
              return false;
          },
          error: function (xhr, ajaxOptions, thrownError) {
              if (xhr.status !== 0) {
                  showServicePopupWindow(
                          "Ошибка " + xhr.status,
                          "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError
                          );
              }
              return false;
          }
      });
      return false;
  });

  $(".searchpanelclose").click(function () {
      $("#searchpanel").stop().css({top: "-200px"});
      $("#uadress").show();
  });

  $(".open-user-address-form").click(function () {
      $("#searchpanel").stop().animate({top: "0px"});
      $("#uadress").hide();
      $('#useraddress').focus();
  });

  $("#address").click(function () {
      $("#adesserror").hide();
      $("#dadataanswer").slideUp();
      $("#dadataask").delay(500).slideUp();
      $("#ycard").fadeOut();
  });

  $("#useraddress").suggestions({
//      token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
//      type: "ADDRESS",
      onSelect: function (suggestion) {
          $("#adesserror").hide();
          //console.log(suggestion.data);
          if (!suggestion.data.house)
          {
              $("#useradesserror").html("Необходимо указать адрес до номера дома!").show();
              return false;
          }
          var dataString = JSON.stringify(suggestion);
          $("#geodatadadata").val(dataString);
          getLegalStores(dataString, '#useradesserror');
          addUserAddrees(dataString, $("#useraddress").val());
      }
  });

});
