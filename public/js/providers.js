function getProdeuctCategory(){
    var brandId = $("#baseId").val();
    var categoryId = $("#categoryId").val();

    //$("#quick-menu-item-" + categoryId ).addClass("active");
    $.ajax({
            beforeSend : function (){
                },
            url: "/ajax-get-products-provider",
            type: 'POST',
            cache: false,
            data: {"providerId": brandId, "categoryId": categoryId },
            success: function (data) {
             //showAjaxErrorPopupWindow ("id", categoryId );
             $("#providercontent").html(JSON.stringify(data,true,2));
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

$(document).ready(function () {
  getProdeuctCategory();

});

$(document).ready(function(){

  if ($('#provider-page').length){

    var categoryPage = new Vue({
      el: '#store-page',
      data: {
        products: [],
        length: 0,
        providerId: $("#baseId").val(),
        categoryId: $("#categoryId").val()
      },
      created() {
          this.category_id = window.location.href.split("/").slice(-1)[0],
          axios
            .post('/ajax-get-products-provider',
              Qs.stringify({
                categoryId : this.categoryId,
                providerId: this.storeId
              }))
            .then(response => {
              console.log(response);
              this.products = response.data;
              this.length = Object.keys(this.products).length
              console.log('Products', this.products);
            });
      }
    });

  }

});
