// function getClientProducts (type){
//     var url = "/ajax-get-client-" + type;
//      $.ajax({
//             url: url,
//             success: function (data) {
//              $("#client-" + type ).html(JSON.stringify(data,  null, 2 ));
//             },
//             error: function (xhr, ajaxOptions, thrownError) {
//                  showAjaxErrorPopupWindow(xhr.status, thrownError);
//                 return false;
//             }
//         });
// }
//
// $(function () {
//     getClientProducts ("favorites");
//     getClientProducts ("history");
// });


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
        countPages() {
          let pages = this.productsCount / this.productsLimit;
          this.productsPages = Math.ceil(pages);
        },
        setUnit() {
          if (this.productsCount.toString().slice(-1) == '1' && this.productsCount.toString().slice(-2) != '11'){
            this.productsCountUnit = 'товар';
          } else if (+this.productsCount.toString().slice(-1) > 1 && +this.productsCount.toString().slice(-1) < 5 && +this.productsCount.toString().slice(-2) != '12' && this.productsCount.toString().slice(-2) != '13' && this.productsCount.toString().slice(-2) != '14') {
            this.productsCountUnit = 'товара';
          } else {
            this.productsCountUnit = 'товаров';
          }
        },
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


  if ($('#user-viewed-products').length){

    var categoryPage = new Vue({
      el: '#user-viewed-products',
      data: {
        products: [],
        length: 0
      },
      created() {
          axios
            .post('/ajax-get-client-history')
            .then(response => {
              if (response.data.products){
                this.products = response.data.products;
                this.length = Object.keys(this.products).length;
              }
              console.log('Истор',this.length,this.products);
            });
      },
      updated() {
        setTimeout(() => {
          $('#user-viewed-products .products-carousel').slick(
            {
              infinite: true,
              slidesToShow: 4,
              slidesToScroll: 4
            }
          );
        }, 200);
      }
    });

  }

});
