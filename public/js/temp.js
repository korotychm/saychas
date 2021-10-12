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

  if ($('#testimonials').length){

    var testimonials = new Vue({
      el: '#testimonials',
      data: {
        product_id: '',
        reviews: [],
        overage_rating: 0,
        images_path: '',
        statistics: {},
        reviewsImages: []
      },
      methods: {
        getImages(){
          for (review in this.reviews){
            for (image in review.images){
              this.reviewsImages.push(image);
            }
          }
        }
      },
      created() {
          this.product_id = $('#testimonials').data('id'),
          axios
            .post('/ajax-get-product-review',
              Qs.stringify({
                productId : this.product_id
              }))
            .then(response => {
              this.statistics = response.data.statistics;
              this.overage_rating = response.data.overage_rating;
              this.images_path = response.data.images_path;
              this.reviews = response.data.reviews;
              this.getImages();
            });
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
