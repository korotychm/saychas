const Products = {
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
          <div class="filter__select">
            <div class="custom-select custom-select--radio">
              <div class="custom-select__label input">Все бренды</div>
              <div class="custom-select__dropdown">
                <div class="custom-select__dropdown-inner">
                  <label class="custom-select__option">
                    <input type="radio" checked="checked" value="" name="brand_filter" v-model="selectedFilters.brand_id" @change="loadPage()" />
                    <span>Все бренды</span>
                  </label>
                  <label v-for="brand in filters.brands" class="custom-select__option">
                    <input type="radio" :checked="(brand[0] === selectedFilters.brand_id)" :value="brand[0]" name="brand_filter" v-model="selectedFilters.brand_id" @change="loadPage()" />
                    <span>{{brand[1]}}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="filter__btn">
              <router-link to="/product-add" class="btn btn--primary">+ Добавить товары</router-link>
          </div>
        </div>

        <div class="cp-container products list">
          <div class="thead">
            <div class="td"></div>
            <div class="td">Наименование</div>
            <div class="td">Категория</div>
            <div class="td">Бренд</div>
            <div class="td">Статус</div>
          </div>
          <div class="tbody">
              <router-link :to="'/products/' + product.id" v-for="product in products" class="tr">
                  <div class="td products__img product-small-img">
                    <img :src="(product.images.length) ? (((product.moderated) ? imgPathModerated : imgPath) + product.images[0]) : '/img/ui/nophoto.jpg'" />
                  </div>
                  <div class="td td--hover">
                    <div class="products__title">
                      {{ product.title }}
                      <span class="products__art">Арт.: {{ product.vendor_code }}</span>
                    </div>
                  </div>
                  <div class="td products__category">
                      <div>{{ product.mother_categories[1][1] }}</div>
                      <div>{{ product.category_name }}</div>
                  </div>
                  <div class="td">{{ product.brand_name }}</div>
                  <div class="td">
                  <span class="product__status" v-if="product.moderated">
                    <span class="product__status-circle product__status-circle--0"></span>
                  </span>
                  <span class="product__status" v-if="!product.moderated && product.processed">
                    <span class="product__status-circle product__status-circle--2"></span>
                  </span>
                  <span class="product__status product__status--1" v-if="!product.moderated && !product.processed">
                    <span class="product__status-circle product__status-circle--1"></span>
                  </span>
                  </div>
              </router-link>
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
        products: {},
        pages: 1,
        filters: {},
        imgPath: productImgPath,
        imgPathModerated: productImgPathModerated,
        selectedFilters: {
          brand_id: '',
          category_id: ''
        },
        search: '',
        filtersCreated: false
      }
  },
  methods: {
    getProducts() {
      let requestUrl = '/control-panel/show-products';
      if (this.filtersCreated) {
        requestUrl = '/control-panel/show-products-from-cache';
      }
      const headers = { 'X-Requested-With': 'XMLHttpRequest' }
      console.log(          Qs.stringify({
        page_no : this.page_no,
        rows_per_page : this.rows_per_page,
        filters: this.selectedFilters,
        search: this.search,
        use_cache: this.filtersCreated
      }))
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
              console.log(response.data);
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
