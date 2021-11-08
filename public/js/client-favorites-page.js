function getClientProducts (type){
    var url = "/ajax-get-client-" + type;
     $.ajax({
            url: url,
            success: function (data) {
             $("#client-" + type ).html(JSON.stringify(data,  null, 2 ));
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
}

$(function () {
    getClientProducts ("favorites");
    getClientProducts ("history");
});


$(document).ready(function(){

  if ($('#user-favorites').length){

    var categoryPage = new Vue({
      el: '#user-favorites',
      data: {
        products: [],
        length: 0,
        sort: 0,
        productsCount: 0,
        productsCountUnit: 'товаров',
        productsLimit: 0,
        currentPage: 0
      },
      watch: {
        sort() {
          this.currentPage = 0;
          this.getProducts();
        }
      },
      methods: {
        getProducts(){
          axios
            .post('/ajax-get-client-favorites',
              Qs.stringify({
                sort: this.sort,
                page: this.currentPage
              }))
            .then(response => {
              console.log(response);
              this.products = response.data.products;
              this.productsCount = response.data.count;
              this.productsLimit = response.data.limit;
              console.log('Favorites', this.products);
              this.setUnit();
              this.countPages();
            });
        },
        loadPage(index) {
          this.currentPage = index;
          this.getProducts();
        }
      },
      created() {
          this.category_id = window.location.href.split("/").slice(-1)[0],
          axios
            .post('/ajax-get-client-favorites',
              Qs.stringify({
                categoryId : this.categoryId,
                storeId: this.storeId
              }))
            .then(response => {
              this.getProducts();
              console.log('Favorites', this.products);
            });
      }
    });

  }

});
