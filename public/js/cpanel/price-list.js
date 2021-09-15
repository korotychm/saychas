const PriceList = {
  data: function () {
    return {
      htmlContent: '',
      page_no: 1,
      rows_per_page: 2,
      products: {},
      pages: 1,
      filters: {},
      imgPath: productImgPath,
      selectedFilters: {
        category_id: ''
      },
      search: '',
      filtersCreated: false
    }
  },
  template:
    `<div>
      <div v-if="htmlContent" v-html="htmlContent"></div>
      <div v-else>
        <div class="filter">
          <form class="filter__search" @submit.prevent="loadPage()">
            <input class="input input--white" type="text" v-model="search" placeholder="Быстрый поиск" />
            <button>
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" />
              </svg>
            </button>
          </form>
          <div class="filter__select">
            <select class="select select--white" v-model="selectedFilters.category_id" value="" @change="loadPage()">
              <option value="" selected >Все категории</option>
              <option v-for="category in filters.categories" :value="category[0]">{{ category[1] }}</option>
            </select>
          </div>
          <div class="filter__btn">
            <a class="btn btn--secondary" href="#">Скачать список</a>
          </div>
          <div class="filter__btn">
            <a class="btn btn--primary">Загрузить список</a>
          </div>
        </div>

        <div class="cp-container pricelist list">
          <div class="thead">
            <div class="td"></div>
            <div class="td">Наименование</div>
            <div class="td">Категория</div>
            <div class="td">Скидка</div>
            <div class="td">Стоимость</div>
          </div>
          <div class="tbody">
              <div v-for="product in products" class="tr pricelist__item">
                  <div class="td pricelist__img">
                    <img :src="(product.images.length) ? (imgPath + product.images[0]) : '/images/products/nophoto.jpg'" />
                  </div>
                  <div class="td pricelist__title">
                    <a>{{ product.title }}</a>
                  </div>
                  <div class="td pricelist__category">
                      <div>{{ product.category_name }}</div>
                  </div>
                  <div class="td">0%</div>
                  <div class="td">
                    {{ (product.price / 100).toLocaleString() }} ₽
                  </div>
                  <div class="pricelist__popup">
                    <div class="pricelist__popup-category">Техника для дома</div>
                    <div class="pricelist__popup-inputs">
                      <div class="pricelist__popup-input-group">
                        <div class="pricelist__popup-sale">
                          <input type="number" max="99" min="0" value="0" />
                        </div>
                        <div class="pricelist__popup-price">
                          <input type="number" min="0" :value="product.price / 100" />
                        </div>
                      </div>
                      <p>Изменение цен и скидок происходит раз в сутки - в 03:00</p>
                    </div>
                    <div class="pricelist__popup-right">
                      <div class="pricelist__popup-total">
                        <p>Итого<br> с учетом скидки</p>
                        <h3>{{ (product.price / 100).toLocaleString() }} ₽</h3>
                      </div>
                      <button class="btn btn--primary">Применить</button>
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
  methods: {
    getProducts() {
      let requestUrl = '/control-panel/show-products';
      if (this.filtersCreated) {
        requestUrl = '/control-panel/show-products-from-cache';
      }
      const headers = { 'X-Requested-With': 'XMLHttpRequest' }
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page,
            filters: this.selectedFilters,
            search: this.search,
            use_cache: this.filtersCreated
          }), {headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.pages = response.data.data.limits.total;
              this.products = response.data.data.body;
              if (!this.filtersCreated){
                this.filters = response.data.data.filters;
                this.filtersCreated = true;
              }
              console.log(this.products);
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
      this.getProducts();
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getProducts();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}

$(document).on('click','.pricelist__title',function(){
  $('.pricelist__item').removeClass('active');
  $(this).parent().addClass('active');
});

$(document).mouseup(function(e)
{
    var container = $(".pricelist__popup");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.parent().removeClass('active');
    }
});