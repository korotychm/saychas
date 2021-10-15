$(document).ready(function(){

  if ($('#cats').length){
    var cats = new Vue({
      el: '#cats',
      data: {
        categoryTree: categoryTree,
        currentCat: 0
      },
      created() {
        console.log(this.categoryTree);
      }
    });
  }

  if ($('#product-card').length){

    var testimonials = new Vue({
      el: '#product-card',
      data: {
        product_id: '',
        reviews: [],
        average_rating: 0,
        images_path: '',
        statistic: {},
        reviewsPhotos: []
      },
      computed: {
        reviewsUnit(){
          let length = this.reviews.length.toString()
          if (length.slice(-1) == '1' && length.slice(-2) != '11'){
            return 'отзыв';
          } else if (+length.slice(-1) > 1 && +length.slice(-1) < 5 && length.slice(-2) != '12' && length.slice(-2) != '13' && length.slice(-2) != '14'){
            return 'отзыва';
          }
          return 'отзывов';
        }
      },
      methods: {
        statisticsPercent(grade){
          let cumulative = 0;
          for (item in this.statistic){
            cumulative += +this.statistic[item]
          }
          return this.statistic[grade] / cumulative * 100;
        },
        getImages(){
          for (review of this.reviews){
            console.log('review',review);
            for (image of review.images){
              this.reviewsPhotos.push(image);
            }
          }
        }
      },
      created() {
          this.product_id = $('#testimonials').data('id'),
          console.log('productId',this.product_id);
          axios
            .post('/ajax-get-product-review',
              Qs.stringify({
                productId : this.product_id
              }))
            .then(response => {
              console.log(response);
              this.statistic = response.data.statistic;
              this.average_rating = response.data.average_rating;
              this.images_path = response.data.images_path;
              this.reviews = response.data.reviews;
              this.getImages();
              console.log(this.reviews);
            });
      },
      mounted() {
        zoomImg();
      },
      updated() {
        $('.testimonials__photos--carousel').slick(
          {
            infinite: false,
            slidesToShow: 10,
            slidesToScroll: 1
          }
        );
      }
    });

  }


});


function addProductToFavorites (productId, callback) {
     $.ajax({
            url: "/ajax/add-to-favorites",
            cache: false,
            type: 'POST',
            data: {'productId': productId},
            success: function (data) {
                console.log(data);
                if (!data.result) {showAjaxErrorPopupWindow("", data.description ); return;}
                callback.addClass("active");
                callback.children("span").text(data.lable);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
  }
 function removeProductFromFavorites (productId, callback) {
     $.ajax({
            url: "/ajax/remove-from-favorites",
            cache: false,
            type: 'POST',
            data: {'productId': productId},
            success: function (data) {
                console.log(data);
                if (!data.result) {showAjaxErrorPopupWindow("", data.description ); return;}
                callback.removeClass("active");
                callback.children("span").text(data.lable);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                 showAjaxErrorPopupWindow(xhr.status, thrownError);
                return false;
            }
        });
  }


$(document).ready(function () {

  $(document).on('click', '.wishlist-icon',function(e){
      e.preventDefault();
      var callback = $(this);
      if (callback.hasClass("active")){
          removeProductFromFavorites(callback.data("id"), callback);
      }
      else {
          addProductToFavorites(callback.data("id"), callback);
      }
  });

});
