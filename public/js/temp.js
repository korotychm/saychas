$(document).ready(function(){

  if ($('#cats').length){
    var cats = new Vue({
      el: '#cats',
      data: {
        categoryTree: categoryTree,
        currentCat: 0
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
        thumbnails_path: '',
        statistic: {},
        images: [],
        limit: {},
        sortBy: 0,
        currentPage: 1,
        reviews_count: 0,
        reviewsImages: false,
        reviewer: null,
        reviewFormGrade: 0,
        reviewFormText: '',
        reviewFormError: '',
        reviewFormImages: [''],
        reviewFormImagesLimit: 6,
        showReviewFormAddImg: true,
        reviewData: new FormData(),
        reviewSent: false
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
                page : this.currentPage - 1,
                sort: this.sortBy
              }))
            .then(response => {
              const mergedReviews = [...this.reviews, ...response.data.reviews];
              this.reviews = mergedReviews;
            });
        },
        getReviews() {
          axios
            .post('/ajax-get-product-review',
              Qs.stringify({
                productId : this.product_id,
                page : this.currentPage - 1,
                sort: this.sortBy
              }))
            .then(response => {
              this.statistic = response.data.statistic;
              this.average_rating = response.data.average_rating;
              this.images_path = response.data.images_path;
              this.thumbnails_path = response.data.thumbnails_path;
              this.images = response.data.images;
              this.limit = response.data.limit;
              this.reviewer = response.data.reviewer;
              this.reviews_count = response.data.reviews_count;
              this.reviews = response.data.reviews;
            });
        },
        showReviewForm() {
          $('#reviewPopup').fadeIn();
        },
        setFormGrade(grade){
          this.reviewFormGrade = grade;
        },
        addFormImage(){
          var index = this.reviewFormImages.length - 1;
          var file = document.querySelector('#addImgBtn').files[0];
          var newImg = '';
          var reader = new FileReader();
          reader.onload = (e) => {
            this.$set(this.reviewFormImages, index, e.target.result);
            if (this.reviewFormImages.length < this.reviewFormImagesLimit) {
              this.reviewFormImages.push('');
            } else {
              this.showReviewFormAddImg = false;
            }
          }
          reader.readAsDataURL(file);
          this.reviewData.append('files[]', file);
        },
        delFormImage(index){
          this.reviewFormImages.splice(index, 1);
          var files = this.reviewData.getAll('files[]');
          files.splice(index,1);
          this.reviewData.delete('files[]');
          files.forEach(file => this.reviewData.append('files[]', file));
          this.showReviewFormAddImg = true;
        },
        sendReviewForm() {
          if (this.reviewFormGrade){
            this.reviewData.append('productId', this.product_id);
            this.reviewData.append('rating', this.reviewFormGrade - 1);
            this.reviewData.append('reviewMessage', this.reviewFormText);
            axios.post('/ajax-set-product-review', this.reviewData, {
                  headers: {
                    'Content-Type': 'multipart/form-data'
                  }
            })
            .then(response => {
              $('#reviewPopup').fadeOut();
              this.reviewSent = true;
            })
            .catch(error => {
              console.log(error.response)
            })
          } else {
            this.reviewFormError = 'Пожалуйста, поставьте оценку товару.'
          }
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
          return Math.round(this.statistic[grade] / cumulative * 100);
        }
      },
      created() {
        this.product_id = $('#testimonials').data('id');
        this.getReviews()
      },
      mounted() {
        zoomImg();
      },
      updated() {
        if (!this.reviewsImages){
          this.reviewsImages = true;
          $('.testimonials__photos--carousel').slick(
            {
              infinite: false,
              slidesToShow: 5,
              slidesToScroll: 1,
              mobileFirst: true,
              responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 10,
                    slidesToScroll: 1
                  }
                }
              ]
            }
          );
        }
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
