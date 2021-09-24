// function getProdeuctCategory(){
//     var categoryId = window.location.href.split("/").slice(-1)[0];
//     $("#quick-menu-item-" + categoryId ).addClass("active");
//     $.ajax({
//             beforeSend : function (){
//                 },
//             url: "/ajax-get-products-categories",
//             type: 'POST',
//             cache: false,
//             data: {"categoryId": categoryId},
//             success: function (data) {
//              //showAjaxErrorPopupWindow ("id", categoryId );
//              $("#category-list").html(JSON.stringify(data,true,2));
//               return false;
//             },
//             error: function (xhr, ajaxOptions, thrownError) {
//              if (xhr.status !== 0) {
//                     showAjaxErrorPopupWindow (xhr.status, thrownError);
//                 }
//                 return false;
//             }
//         });
//     return false;
// }
//
// $(document).ready(function () {
//   getProdeuctCategory();
//
// });

$(document).ready(function(){

  if ($('#category-page').length){

    var categoryPage = new Vue({
      el: '#category-page',
      data: {
        category_id: '',
        products: []
      },
      created() {
          this.category_id = window.location.href.split("/").slice(-1)[0],
          axios
            .post('/ajax-get-products-categories',
              Qs.stringify({
                categoryId : this.category_id
              }))
            .then(response => {
              console.log(response);
              this.products = response.data;
              console.log('Products', this.products);
            });
      }
    });

  }

});
