
const Analytics = { template: '<div>Аналитика</div>' }

const Products = {
  data: function () {
    return {
      page_no: 1,
      rows_per_page: 2,
      products: {},
      pages: 1,
      filters: {},
      imgPath: productImgPath,
      selectedFilters: {
        brand_id: '',
        category_id: ''
      },
      search: '',
      filtersCreated: false
    }
  },
  template:
    `<div>
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
        <div class="filter__select">
          <select class="select select--white" v-model="selectedFilters.brand_id" value="" @change="loadPage()">
            <option value="" selected >Все бренды</option>
            <option v-for="brand in filters.brands" :value="brand[0]">{{ brand[1] }}</option>
          </select>
        </div>
        <div class="filter__btn">
          <a class="btn btn--primary" href="/product-add.html">+ Добавить товары</a>
        </div>
      </div>

      <div class="products__list">
        <div class="thead">
          <div class="td"></div>
          <div class="td">Наименование</div>
          <div class="td">Категория</div>
          <div class="td">Бренд</div>
        </div>
        <div class="tbody">
            <div v-for="product in products" class="tr">
                <div class="td products__img">
                  <img :src="(product.images.length) ? (imgPath + product.images[0]) : '/images/products/nophoto.jpg'" />
                </div>
                <div class="td products__title">
                  <router-link :to="'/products/' + product.id">{{ product.title }}</router-link>
                </div>
                <div class="td products__category">
                    <div>{{ product.category_name }}</div>
                </div>
                <div class="td">{{ product.brand_name }}</div>
            </div>
        </div>
      </div>
      <div class="pagination">
        <a v-for="index in pages" :class="{active : (index == page_no)}" @click="loadPage(index)">{{ index }}</a>
      </div>
    </div>`,
  methods: {
    getProducts() {
      let requestUrl = '/control-panel/show-products';
      if (this.filtersCreated) {
        requestUrl = '/control-panel/show-products-from-cache';
      }
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page,
            filters: this.selectedFilters,
            search: this.search,
            use_cache: this.filtersCreated
          }))
          .then(response => {
            console.log(response.data);
            this.pages = response.data.data.limits.total;
            this.products = response.data.data.body;
            if (!this.filtersCreated){
              this.filters = response.data.data.filters;
              this.filtersCreated = true;
            }
          });
    },
    loadPage(index = 1) {
      this.page_no = index;
      this.getProducts();
    }
  },
  created: function(){
    this.getProducts();
  }
}

const Stores = {
  template: `<div>
    <div class="filter">
      <form class="filter__search" @submit.prevent="loadPage()">
        <input class="input input--white" type="text" v-model="search" placeholder="Быстрый поиск" />
        <button>
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" />
          </svg>
        </button>
      </form>
      <div class="filter__select">
        <select class="select select--white" v-model="selectedFilters.status_id" value="" @change="loadPage()">
          <option value="" selected >Все статусы</option>
          <option v-for="status in filters.statuses" :value="status[0]">{{ status[1] }}</option>
        </select>
      </div>
      <div class="filter__btn">
        <a class="btn btn--primary" href="">+ Добавить магазин</a>
      </div>
    </div>

    <div class="products__list">
      <div class="thead">
        <span></span>
        <div class="td">Наименование</div>
        <div class="td">Адрес</div>
        <div class="td">Статус</div>
      </div>
      <div class="tbody">
          <div v-for="store in stores" class="tr">
              <span></span>
              <div class="td products__title">
                  <router-link :to="'/stores/' + store.id">{{ store.title }}</router-link>
              </div>
              <div class="td products__category">
                  <div>{{ store.address }}</div>
              </div>
              <div class="td">
                {{ store.status_name }}
              </div>
          </div>
      </div>
    </div>
    <div class="pagination">
      <a v-for="index in pages" :class="{active : (index == page_no)}" @click="loadPage(index)">{{ index }}</a>
    </div>
  </div>`,
  data: function () {
    return {
      page_no: 1,
      rows_per_page: 2,
      stores: {},
      pages: 1,
      filters: {},
      selectedFilters: {
        status_id: ''
      },
      search: '',
      filtersCreated: false,
      responsedata: ''
    }
  },
  methods: {
    getStores() {
      let requestUrl = '/control-panel/show-stores';
      if (this.filtersCreated) {
        requestUrl = '/control-panel/show-stores-from-cache';
      }
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page,
            filters: this.selectedFilters,
            search: this.search,
            use_cache: this.filtersCreated
          }))
          .then(response => {
            console.log(response.data);
            this.pages = response.data.data.limits.total;
            this.stores = response.data.data.body;
            if (!this.filtersCreated){
              this.filters = response.data.data.filters;
              this.filtersCreated = true;
            }
          });
    },
    loadPage(index = 1) {
      this.page_no = index;
      this.getStores();
    }
  },
  created: function(){
    this.getStores();
  }
}

const ProductEdit = {
  template: `<div class="product">
                <div v-if="editable">
                  <div class="product__category">
                      <h2>Категория</h2>
                      <div class="search-select">
                          <input class="input search-select__input" type="text" value="" v-model="categorySearch" />
                          <div class="search-select__suggestions">
                              <div v-if="!categorySearch" class="search-select__empty">Начните вводить название категории для поиска</div>
                              <div v-if="(categorySearch && !categorySearchResults)" class="search-select__empty">Ничего не найдено</div>
                              <div v-if="categorySearchResults">
                                <label>
                                  <input type="radio" name="suggest" @change="selectCategory('Конечная категория')" />
                                  <span class="search-select__suggestion">
                                  <span class="search-select__suggestion-category--parent">Родительская категория</span>
                                  <span class="search-select__suggestion-category">Конечная категория</span>
                                  </span>
                                </label>
                                <label>
                                  <input type="radio" name="suggest" @change="selectCategory('Конечная категория')" />
                                  <span class="search-select__suggestion">
                                  <span class="search-select__suggestion-category--parent">Родительская категория</span>
                                  <span class="search-select__suggestion-category">Конечная категория</span>
                                  </span>
                                </label>
                                <label>
                                  <input type="radio" name="suggest" @change="selectCategory('Конечная категория')" />
                                  <span class="search-select__suggestion">
                                  <span class="search-select__suggestion-category--parent">Родительская категория</span>
                                  <span class="search-select__suggestion-category">Конечная категория</span>
                                  </span>
                                </label>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="product__info">
                      <div class="product__attribute">
                          <h2>Название товара</h2>
                          <input class="input" type="text" :value="'Товар с id ' + $route.params.id" />
                      </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот товар</div>
            </div>`,
  data: function () {
    return {
      editable: true,
      categorySearch: '',
      categories: [
        {
          id: 1,
          name: 'Женская одежда',
          childs: [
            {
              id: 2,
              name: 'Белье',
              childs: [
                {
                  id: 3,
                  name: 'Трусы'
                }
              ]
            }
          ]
        },
        {
          id: 4,
          name: 'Мужская одежда',
          childs: [
            {
              id: 5,
              name: 'Белье',
              childs: [
                {
                  id: 6,
                  name: 'Трусы'
                }
              ]
            }
          ]
        },
        {
          id: 7,
          name: 'Электроника',
          childs: [
            {
              id: 8,
              name: 'Смартфоны и смарт-часы',
              childs: [
                {
                  id: 9,
                  name: 'Смарт-часы'
                }
              ]
            }
          ]
        }
      ]
    }
  },
  computed: {
    categorySearchResults(){
      return false;
    }
  },
  methods: {
    getStores() {
      let requestUrl = '/control-panel/'
      axios
        .post(requestUrl,
          Qs.stringify({
            product_id : this.$route.params.id
          }))
          .then(response => {
            console.log(response);
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.editable = false;
            }
          });
    },
    selectCategory(value) {
      this.categorySearch = value;
    }
  },
  created: function(){
    this.getStores();
  }
}

const StoreEdit = { template: '<div>Магазин с id {{ $route.params.id }}</div>' }

const routes = [
  {
    name: 'analytics',
    path: '/analytics',
    meta: {
      h1: 'Аналитика'
    },
    component: Analytics
  },
  {
    name: 'products',
    path: '/products',
    meta: {
      h1: 'Мои товары'
    },
    component: Products
  },
  {
    name: 'stores',
    path: '/stores',
    meta: {
      h1: 'Магазины'
    },
    component: Stores
  },
  {
    name: 'product-edit',
    path: '/products/:id',
    meta: {
      h1: 'Редактирование товара',
      back_route: '/products'
    },
    component: ProductEdit
  },
  {
    name: 'store-edit',
    path: '/stores/:id',
    meta: {
      h1: 'Редактирование магазина',
      back_route: '/stores'
    },
    component: StoreEdit
  }
]



const router = new VueRouter({
  routes,
  //mode: 'history'
})

const cp = new Vue({
  router,
  mounted: function(){
    //router.replace('/analytics');
  }
}).$mount('#cp')
