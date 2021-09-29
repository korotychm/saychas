const Inventory = {
  template:
    `<div>
      <div v-if="htmlContent" v-html="htmlContent"></div>
      <div v-else>
        <!-- фильтр -->
        <div class="filter">
          <div class="filter__select" style="width: 400px; margin: 0 10px 0 0;">
            <div class="custom-select custom-select--radio">
              <div class="custom-select__label input">Выберите магазин</div>
              <div class="custom-select__dropdown">
                <div class="custom-select__dropdown-inner">
                  <label v-for="store in stores" class="custom-select__option">
                    <input type="radio" :checked="(store.id === selectedFilters.store_id)" :value="store.id" name="category_filter" v-model="selectedFilters.store_id" @change="loadPage()" />
                    <span>{{store.title}}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <form v-if="filtersCreated" class="filter__search" @submit.prevent="loadPage()">
            <input class="input input--white" type="text" v-model="search" placeholder="Быстрый поиск" />
            <button>
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" />
              </svg>
            </button>
          </form>
          <div v-if="filtersCreated" class="filter__select">
            <div class="custom-select custom-select--radio">
              <div class="custom-select__label input">Все категории</div>
              <div class="custom-select__dropdown">
                <div class="custom-select__dropdown-inner">
                  <label class="custom-select__option">
                    <input type="radio" checked="checked" value="" name="category_filter" v-model="selectedFilters.category_id" @change="loadPage()" />
                    <span>Все категории</span>
                  </label>
                  <label v-for="category in filters.categories" class="custom-select__option">
                    <input type="radio" :checked="(category[0] === selectedFilters.category_id)" :value="category[0]" name="category_filter" v-model="selectedFilters.category_id" @change="loadPage()" />
                    <span>{{category[1]}}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- фильтр end -->
        <div v-if="filtersCreated" class="cp-container products list inventory">
          <div class="thead">
            <div class="td"></div>
            <div class="td">Наименование</div>
            <div class="td">Категория</div>
            <div class="td">Количество</div>
          </div>
          <div class="tbody">
              <div v-for="(product, index) in products" class="tr inventory__item">
                  <div class="td products__img product-small-img inventory__img">
                    <img :src="(product.images.length) ? (((product.moderated) ? imgPathModerated : imgPath) + product.images[0]) : '/img/ui/nophoto.jpg'" />
                  </div>
                  <div class="td td--hover inventory__title">
                    {{ product.title }}
                  </div>
                  <div class="td products__category inventory__category">
                      <div>{{ product.category_name }}</div>
                  </div>
                  <div class="td inventory__quantity">{{ product.quantity }}</div>
                  <div class="inventory__popup">
                    <div class="inventory__quantity-input">
                      <input type="number" class="input input--number" v-model="product.quantity"/>
                      <button class="btn btn--primary" @click="saveProduct(index)">
                        <svg
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="27px" height="19px">
                        <path fill-rule="evenodd"  fill="currentColor"
                         d="M3.343,9.721 L9.778,16.156 C10.363,16.741 10.363,17.691 9.778,18.277 C9.192,18.863 8.242,18.863 7.656,18.277 L1.221,11.842 C0.635,11.256 0.635,10.306 1.221,9.721 C1.807,9.135 2.757,9.135 3.343,9.721 Z"/>
                        <path fill-rule="evenodd"  fill="currentColor"
                         d="M25.571,2.488 L10.519,17.541 C9.950,18.110 9.27,18.110 8.458,17.541 C7.889,16.971 7.889,16.49 8.458,15.479 L23.510,0.427 C24.79,0.141 25.2,0.141 25.571,0.427 C26.140,0.996 26.140,1.919 25.571,2.488 Z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
              </div>
          </div>
        </div>
        <div v-if="filtersCreated" class="pagination">
          <a v-for="index in pages" :class="{active : (index == page_no)}" @click="loadPage(index)">{{ index }}</a>
        </div>
      </div>
    </div>`,
 data: function () {
      return {
        htmlContent: '',
        page_no: 1,
        rows_per_page: 10,
        stores: {},
        pages: 1,
        filters: {},
        imgPath: productImgPath,
        imgPathModerated: productImgPathModerated,
        selectedFilters: {
          category_id: '',
          store_id: ''
        },
        search: '',
        filtersCreated: false,
        products: {}
      }
  },
  methods: {
    saveProduct(index) {
      let requestUrl = '/control-panel/update-stock-balance';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      if (!this.products[index].quantity === ""){
        showServicePopupWindow('Невозможно сохранить изменения', 'Пожалуйста, заполните количество');
      } else {
        let request = [
          {
            store_id: this.selectedFilters.store_id,
            products: [
              {
                product_id: this.products[index].id,
                quantity: this.products[index].quantity
              }
            ]
          }
        ];
        axios
          .post(requestUrl,
            Qs.stringify({
              data: request
            }),
            {
              headers
            })
            .then(response => {
              console.log('Ответ на сохранение остатков', response.data);
              if (response.data.result){
                $('.inventory__item').removeClass('active');
              }
            })
            .catch(error => {
              console.log(error);
              if (error.response.status == '403'){
                location.reload();
              }
            });
      }
    },
    getStores() {
      let requestUrl = '/control-panel/show-stores';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : 1,
            rows_per_page : 10000,
            filters: {
              status_id: ''
            },
            search: '',
            use_cache: false
          }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.stores = response.data.data.body;
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
    },
    getProducts() {
      let requestUrl = '/control-panel/show-stock-balance';
      // if (this.filtersCreated) {
      //   requestUrl = '/control-panel/show-products-from-cache';
      // }
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
            console.log('Ответ после выбора магазина',response.data);
            if (response.data.data === true) {
              location.reload();
            } else {
              this.pages = response.data.data.limits.total;
              this.products = response.data.data.body;
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
    }
  },
  mounted: function(){
    this.getStores();
    $('.main__loader').hide();
  }
}
