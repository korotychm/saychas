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
        images: [],
        limit: {},
        sortBy: 2,
        currentPage: 1,
        reviews_count: 0
      },
      computed: {
        reviewsUnit(){
          let length = this.reviews_count.toString()
          if (length.slice(-1) == '1' && length.slice(-2) != '11'){
            return 'отзыв';
          } else if (+length.slice(-1) > 1 && +length.slice(-1) < 5 && length.slice(-2) != '12' && length.slice(-2) != '13' && length.slice(-2) != '14'){
            return 'отзыва';
          }
          return 'отзывов';
        }
      },
      methods: {
        addReviews() {
          this.currentPage++;
          axios
            .post('/ajax-get-product-review',
              Qs.stringify({
                productId : this.product_id,
                page : this.currentPage - 1
              }))
            .then(response => {
              this.limit = response.data.limit;
              this.reviews.concat(response.data.reviews);
              console.log(this.reviews);
            });
        },
        getReviews() {
          axios
            .post('/ajax-get-product-review',
              Qs.stringify({
                productId : this.product_id,
                page : this.currentPage - 1
              }))
            .then(response => {
              console.log(response);
              this.statistic = response.data.statistic;
              this.average_rating = response.data.average_rating;
              this.images_path = response.data.images_path;
              this.images = response.data.images;
              this.limit = response.data.limit;
              this.reviewer = response.data.reviewer;
              this.reviews_count = response.data.reviews_count;
              this.reviews = response.data.reviews;
              this.getImages();
              console.log(this.reviews);
            });
        },
        sortReviews() {
          this.currentPage = 1;
          this.getReviews();
        },
        statisticsPercent(grade){
          let cumulative = 0;
          for (item in this.statistic){
            cumulative += +this.statistic[item]
          }
          return round(this.statistic[grade] / cumulative * 100);
        }
      },
      created() {
        this.product_id = $('#testimonials').data('id')
        this.getReviews()
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
