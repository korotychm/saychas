$(document).ready(function(){

  if ($('#sale-products').length){

    var categoryPage = new Vue({
      el: '#sale-products',
      data: {
        products: []
      },
      created() {
          axios
            .post('/ajax-get-products-sale')
            .then(response => {
              console.log(response);
              this.products = response.data.products;
            });
      },
      updated() {
        $('#sale-products').slick(
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
        products: []
      },
      created() {
          axios
            .post('/ajax-get-products-top')
            .then(response => {
              console.log(response);
              this.products = response.data.products;
            });
      }
    });

  }

});
