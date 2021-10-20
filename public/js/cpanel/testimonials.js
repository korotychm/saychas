const Testimonials = {
  template: `<div>
    <div v-if="htmlContent" v-html="htmlContent"></div>
    <div v-else>
      <div class="filter">
        <div style="display: flex; justify-content: space-between; width: 100%">
          <div style="display: flex;">
            <div class="filter__btn archive-toggle">
              <a class="btn" :class="selectedFilters.is_archive?'btn--secondary':'btn--primary'" @click="setArchive(false)">Новые</a>
            </div>
            <div class="filter__btn archive-toggle">
              <a class="btn" :class="selectedFilters.is_archive?'btn--primary':'btn--secondary'" @click="setArchive(true)">Архив</a>
            </div>
            <div class="filter__select  filter__select--status">
              <div class="custom-select custom-select--radio">
                <div class="custom-select__label input">Все оценки</div>
                <div class="custom-select__dropdown">
                  <div class="custom-select__dropdown-inner">
                    <label class="custom-select__option">
                      <input type="radio" checked="checked" value="" name="rating_filter" v-model="selectedFilters.status_id" @change="loadPage()" />
                      <span>Все оценки</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cp-container list reviews">
        <div class="thead">
          <div class="td">Отзыв</div>
          <div class="td">Оценка</div>
          <div class="td">Дата</div>
        </div>
        <div class="tbody">
          <div v-for="(review, index) in reviews" class="tr reviews__item">
              <div class="td td--hover reviews__title">
                <div class="products__title">
                  {{ review.productTitle }}
                </div>
              </div>
              <div class="td reviews__rating">
                <div v-for="(n, index) in 5" class="reviews__star" :class="{'active': review.rating > index}">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="17px" height="17px"><path fill-rule="evenodd" fill="currentColor" d="M3.788,16.998 C3.764,16.998 3.740,16.997 3.716,16.995 C3.230,16.947 2.869,16.488 2.916,15.973 L3.351,11.50 L0.224,7.340 C0.102,6.950 0.65,6.355 0.307,6.13 C0.419,5.910 0.557,5.837 0.703,5.803 L5.316,4.709 L7.739,0.457 C7.902,0.171 8.188,0.1 8.506,0.1 C8.664,0.1 8.820,0.46 8.958,0.131 C9.86,0.211 9.194,0.323 9.270,0.457 L11.693,4.720 L16.304,5.803 C16.784,5.917 17.86,6.421 16.977,6.926 C16.944,7.79 16.874,7.222 16.774,7.340 L13.658,11.40 L14.93,15.971 C14.136,16.485 13.773,16.942 13.283,16.987 C13.257,16.990 13.230,16.991 13.204,16.991 C13.84,16.991 12.967,16.966 12.856,16.917 L8.504,14.968 L4.152,16.917 C4.34,16.971 3.913,16.998 3.788,16.998 Z"></path></svg>
                </div>
              </div>
              <div class="td reviews__date">{{ getDate(review.date_created) }}</div>
              <div class="td reviews__text">
                {{ review.reviewText }}
                <div class="reviews__images" v-if="review.images.thumbs.length">
                  <div class="reviews__photo" v-for="(photo, index) in review.images.thumbs">
                    <a :href="review.images.view[index]" :data-fancybox="'gallery' + review.id">
                      <img :src="photo" />
                    </a>
                  </div>
                </div>
              </div>
              <div class="reviews__add-answer">
                <textarea class="textarea" placeholder="Ваш ответ"></textarea>
                <div class="reviews__btns">
                  <button class="btn btn--secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="8px" height="13px">
                        <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M0.903,4.974 L4.974,0.903 C5.560,0.317 6.510,0.317 7.96,0.903 C7.681,1.489 7.681,2.439 7.96,3.25 L3.25,7.96 C2.439,7.681 1.489,7.681 0.903,7.96 C0.318,6.510 0.318,5.560 0.903,4.974 Z" />
                        <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.96,9.974 L3.25,5.903 C2.439,5.317 1.489,5.317 0.903,5.903 C0.318,6.489 0.318,7.439 0.903,8.25 L4.974,12.96 C5.560,12.681 6.510,12.681 7.96,12.96 C7.681,11.510 7.681,10.560 7.96,9.974 Z" />
                    </svg>
                    <span>Вернуться</span>
                  </button>
                  <button class="btn btn--primary" @click="addAnswer">Ответить на отзыв</button>
                </div>
              </div>
              <div class="reviews__popup"></div>
          </div>
        </div>
      </div>
      <div class="pagination">
        <a v-for="index in pages" :class="{active : (index == page_no)}" @click="loadPage(index)">{{ index }}</a>
      </div>
    </div>
  </div>`,
  data: function () {
    return {
      htmlContent: '',
      page_no: 1,
      rows_per_page: 10,
      reviews: {},
      pages: 1,
      filters: {},
      selectedFilters: {
        rating: '',
        is_archive: false
      },
      filtersCreated: false
    }
  },
  methods: {
    getDate(date) {
      let localedDate = new Date(date * 1000).toLocaleDateString("ru-RU");
      return localedDate;
    },
    getReviews() {
      $('.main__loader').show();
      let requestUrl = '/control-panel/show-reviews';
      if (this.filtersCreated) {
        requestUrl = '/control-panel/show-reviews-from-cache';
      }
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page,
            filters: this.selectedFilters,
            use_cache: this.filtersCreated
          }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              console.log(response.data);
              this.pages = response.data.data.limits.total;
              this.reviews = response.data.data.body;
              if (!this.filtersCreated){
                this.filters = response.data.data.filters;
                this.filtersCreated = true;
              }
            }
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.htmlContent = error403template;
            }
            if (error.response.status == '404'){
              this.htmlContent = error404template;
            }
            if (error.response.status == '500'){
              this.htmlContent = error500template;
            }
            $('.main__loader').hide();
          });
    },
    loadPage(index = 1) {
      this.page_no = index;
      this.getReviews();
    },
    setArchive(status) {
      this.selectedFilters.is_archive = status;
    },
    addAnswer() {
      return true;
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getReviews();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}

$(document).on('click','.reviews__btns .btn--secondary', function(){
  $(this).parent().parent().removeClass('active');
});

$(document).on('click','.reviews__item',function(){
  $('.reviews__item').removeClass('active');
  $(this).addClass('active');
});

$(document).mouseup(function(e)
{
    var container = $(".reviews__item.active");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        $(this).removeClass('active');
    }
});
