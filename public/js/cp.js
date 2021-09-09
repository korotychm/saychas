
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
            use_cache: this.filtersCreated,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          }))
          .then(response => {
            console.log(response);
            if (response.data.data) {
              this.pages = response.data.data.limits.total;
              this.products = response.data.data.body;
              if (!this.filtersCreated){
                this.filters = response.data.data.filters;
                this.filtersCreated = true;
              }
            }
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
    $('.main__loader').show();
    this.getStores();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}

const ProductEdit = {
  template: `<div class="product">
                <div v-if="editable">
                  <div class="product__category">
                      <h2>Категория</h2>
                      <div class="search-select">
                          <input class="input search-select__input" type="text" value="selectedCategoryName" v-model="categorySearch" @focusout="checkCategory()" />
                          <div class="search-select__suggestions">
                              <div v-if="!categorySearch" class="search-select__empty">Начните вводить название категории для поиска</div>
                              <div v-if="(categorySearch && !filteredCategories.length)" class="search-select__empty">Ничего не найдено</div>
                              <div v-if="(categorySearch && filteredCategories.length)">
                                <label v-for="category in filteredCategories">
                                  <input type="radio" name="suggest" :checked="(category.id == selectedCategoryId)" />
                                  <span class="search-select__suggestion" @click="selectCategory(category.id, category.name)">
                                    <span class="search-select__suggestion-category--parent">{{category.parent}}</span>
                                    <span class="search-select__suggestion-category">{{category.name}}</span>
                                  </span>
                                </label>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="product__info">
                      <div class="product__attribute">
                          <h2>Бренд</h2>
                            <div class="search-select">
                                <input class="input search-select__input" type="text" value="product.brand_name" v-model="brandSearch" @focusout="checkBrand()" />
                                <div class="search-select__suggestions">
                                    <div v-if="!brandSearch" class="search-select__empty">Начните вводить название бренда для поиска</div>
                                    <div v-if="(brandSearch && !filteredBrands.length)" class="search-select__empty">Ничего не найдено</div>
                                    <div v-if="(brandSearch && filteredBrands.length)">
                                      <label v-for="brand in filteredBrands">
                                        <input type="radio" name="suggest" :checked="(brand.id == selectedBrandId)" />
                                        <span class="search-select__suggestion" @click="selectBrand(brand.id, brand.title)">
                                          <span>{{ brand.title }}</span>
                                        </span>
                                      </label>
                                    </div>
                                </div>
                            </div>
                      </div>
                      <div class="product__attribute">
                      </div>

                      <div class="product__attribute">
                          <h2>Название товара</h2>
                          <input class="input" type="text" :value="product.title" />
                      </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот товар</div>
            </div>`,
  data: function () {
    return {
      editable: true,
      categorySearch: '',
      categories: [],
      categoriesFlat: [],
      selectedCategoryId: '',
      selectedCategoryName: '',
      brands: [
        {
          id: 1,
          title: 'Samsung'
        },
        {
          id: 2,
          title: 'Apple'
        },
        {
          id: 3,
          title: 'DNS'
        }
      ],
      brandSearch: '',
      selectedBrandId: '',
      selectedBrandName: '',
      product: ''
    }
  },
  computed: {
    filteredCategories(){
      if (this.categorySearch.length < 3) return false;
      let categories = this.categoriesFlat;
      categories = categories.filter((category) => {
        return (category.name.toLowerCase().includes(this.categorySearch.toLowerCase()))
      })
      return categories;
    },
    filteredBrands(){
      if (this.brandSearch.length < 3) return false;
      let brands = this.brands;
      brands = brands.filter((brand) => {
        return (brand.title.toLowerCase().includes(this.brandSearch.toLowerCase()))
      })
      return brands;
    }
  },
  methods: {
    flatCategories() {
      let categoriesFlat = [];
      function iterateArray(array, parent) {
        for (category of array){
          if (parent){
            category.parent = parent;
          }
          if (category.children){
            let newParent = category.title;
            if (parent) {
              newParent = category.parent + ' > ' + category.title;
            }
            iterateArray(category.children, newParent);
          } else {
            categoriesFlat.push({
              id: category.id,
              name: category.title,
              parent: category.parent
            })
          }
        }
      }
      iterateArray(this.categories, false);
      this.categoriesFlat = categoriesFlat;
      this.categorySearch = this.categoriesFlat.find(x => x.id === this.product.category_id).name;
      this.selectedCategoryName = this.categorySearch;
      this.selectedCategoryId = this.product.category_id;
      console.log(this.categorySearch, this.product.category_id);
    },
    getProduct() {
      let requestUrl = '/control-panel/edit-product'
      axios
        .post(requestUrl,
          Qs.stringify({
            product_id : this.$route.params.id
          }))
          .then(response => {
            console.log(response.data);
            this.categories = response.data.category_tree;
            this.product = response.data.product;
            this.brandSearch = this.product.brand_name;
            this.selectedBrandId = this.product.brand_id;
            this.selectedBrandName = this.product.brand_name;
            console.log(this.product);
            this.flatCategories();
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.editable = false;
            }
          });
    },
    checkCategory() {
      if (!this.categorySearch){
        this.categorySearch = this.selectedCategoryName;
      }
    },
    checkBrand() {
      if (!this.brandSearch){
        this.brandSearch = this.brandCategoryName;
      }
    },
    selectCategory(id,value) {
      this.selectedCategoryId = id;
      this.categorySearch = value;
      this.selectedCategoryName = value;
    },
    selectBrand(id,value) {
      this.selectedBrandId = id;
      this.brandSearch = value;
      this.selectedBrandName = value;
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getProduct();
  },
  updated: function(){
    $('.main__loader').hide();
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


$(document).on('focusin','.search-select__input',function(){
  $(this).parent().find('.search-select__suggestions').addClass('active').css('pointer-events','auto');
});
$(document).on('focusout','.search-select__input',function(){
  let el = $(this).parent().find('.search-select__suggestions');
  el.removeClass('active')
  setTimeout(function() {
    el.css('pointer-events','none');
  }, 300);
});
