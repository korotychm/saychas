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
              <div class="td td--hover review__title">
                <div class="products__title">
                  {{ review.title }}
                </div>
              </div>
              <div class="td reviews__rating"></div>
              <div class="td reviews__date">{{ reviews.date }}</div>
              <div class="td reviews__text">
                {{ reviews.text }}
                <div class="reviews__images"></div>
              </div>
              <div class="reviews__popup">
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
