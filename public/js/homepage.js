$(document).ready(function(){

  if ($('#sale-products').length){

    var categoryPage = new Vue({
      el: '#sale-products',
      data: {
        products: [],
        length: 0
      },
      created() {
          axios
            .post('/ajax-get-products-sale')
            .then(response => {
              console.log(response);
              this.products = response.data.products;
              this.length = Object.keys(this.products).length;
              console.log('Продукты со скидкой',this.length,this.products);
            });
      },
      updated() {
        $('#sale-products .products-carousel').slick(
          {
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4
          }
        );
      }
    });

  }

  if ($('#popular-products').length){

    var categoryPage = new Vue({
      el: '#popular-products',
      data: {
        products: [],
        length: 0
      },
      created() {
          axios
            .post('/ajax-get-products-top')
            .then(response => {
              this.products = response.data.products;
              this.length = Object.keys(this.products).length;
              console.log('Популярные продукты',this.length,this.products);
            });
      }
    });

  }

  if ($('#viewed-products').length){

    var categoryPage = new Vue({
      el: '#viewed-products',
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
          if (this.length){
            $('#viewed-products .products-carousel').slick(
              {
                infinite: true,
                slidesToShow: 4,
                slidesToScroll: 4
              }
            );
          }
      }
    });

  }

  if ($('#popular-brands').length){

    var categoryPage = new Vue({
      el: '#popular-brands',
      data: {
        brands: [],
        length: 0
      },
      created() {
          axios
            .post('/ajax-get-brands-top')
            .then(response => {
              this.brands = response.data;
              this.length = this.brands.length;
              console.log('Brands',this.length,this.brands);
            });
      },
      updated() {
        $('#viewed-products .products-carousel').slick(
          {
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 4
          }
        );
      }
    });

  }

});
