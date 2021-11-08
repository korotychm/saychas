$(document).ready(function(){

  if ($('#category-page').length){

    var categoryPage = new Vue({
      el: '#category-page',
      data: {
        category_id: '',
        products: [],
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
        getTimePoint(created,timepoint,merged = false) {
          let deliveryTime = 1;
          if (merged) deliveryTime = 3;
        },
        getProducts(){
          axios
            .post('/ajax-get-products-categories',
              Qs.stringify({
                categoryId : this.category_id,
                sort: this.sort,
                page: this.currentPage
              }))
            .then(response => {
              console.log(response);
              this.products = response.data.products;
              this.productsCount = response.data.count;
              this.productsLimit = response.data.limit;
              console.log('Products', this.products);
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
          this.category_id = window.location.href.split("/").slice(-1)[0];
          this.getProducts();
      }
    });

  }

});
