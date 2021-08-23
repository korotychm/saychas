$(document).ready(function () {

  $("#tree").delay(500).slideDown("slow");

  $("#tree").treeview({
      persist: "location",
      collapsed: true,
      animated: "medium"
  });

  $(window).resize(function () {
      leftpanelclose();
  });

  $(".catalogshow").click(function () {
      $("#overcoverblack").fadeIn();
      $("#lefmobiletpanel").animate({left: "0"}, 500);

  });

  $("#lefmobiletpanelclose").click(function () {
      leftpanelclose();
  });

  function leftpanelclose() {
      $("#overcoverblack").fadeOut();
      $("#lefmobiletpanel").stop().animate({left: "-110%"}, 300);
  }

  $("#bascetbottomblok").on("click", ".close", function () {
      $("#bascetbottomblok").slideUp();
  });

});
