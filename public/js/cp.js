var error403template = `<div class="cp-container"><div class="error403"><h2>403</h2><p>Тип вашей учетной записи не позволяет просматривать эту страницу.</p></div></div>`;
var error404template = `<div class="cp-container"><div class="error403"><h2>404</h2><p>Запрашиваемая страница не найдена.</p></div></div>`;
var error500template = `<div class="cp-container"><div class="error403"><h2>404</h2><p>Внутренняя ошибка сервера.</p></div></div>`;

const Orders = {
  template: '<div>Заказы и возвраты</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Analytics = {
  template: '<div>Аналитика</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Products = {
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
        brand_id: '',
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

        <div class="cp-container products list">
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
                  <div class="td">
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

const Stores = {
  template: `<div>
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
          <select class="select select--white" v-model="selectedFilters.status_id" value="" @change="loadPage()">
            <option value="" selected >Все статусы</option>
            <option v-for="status in filters.statuses" :value="status[0]">{{ status[1] }}</option>
          </select>
        </div>
        <div class="filter__btn">
          <a class="btn btn--primary" href="">+ Добавить магазин</a>
        </div>
      </div>
      <div class="cp-container list stores">
        <div class="thead">
          <div class="td">Наименование</div>
          <div class="td">Адрес</div>
          <div class="td">Статус</div>
        </div>
        <div class="tbody">
            <div v-for="store in stores" class="tr">
                <div class="td">
                    <router-link :to="'/stores/' + store.id">{{ store.title }}</router-link>
                </div>
                <div class="td">
                    <div>{{ store.address }}</div>
                </div>
                <div class="td stores__status">
                  <span :class="'stores__status-circle stores__status-circle--' + store.status_id"></span> {{ store.status_name }}
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
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page,
            filters: this.selectedFilters,
            search: this.search,
            use_cache: this.filtersCreated
          }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              console.log(response.data);
              this.pages = response.data.data.limits.total;
              this.stores = response.data.data.body;
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
  template: `<div class="cp-container product">
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
                          <h2>Страна производства</h2>
                            <div class="search-select">
                                <input class="input search-select__input" type="text" value="product.country_name" v-model="countrySearch" @focusout="checkCountry()" />
                                <div class="search-select__suggestions">
                                    <div v-if="!countrySearch" class="search-select__empty">Начните вводить название страны для поиска</div>
                                    <div v-if="(countrySearch && !filteredCountries.length)" class="search-select__empty">Ничего не найдено</div>
                                    <div v-if="(countrySearch && filteredCountries.length)">
                                      <label v-for="country in filteredCountries">
                                        <input type="radio" name="suggest" :checked="(country.id == selectedCountryId)" />
                                        <span class="search-select__suggestion" @click="selectBrand(country.id, country.title)">
                                          <span>{{ country.title }}</span>
                                        </span>
                                      </label>
                                    </div>
                                </div>
                            </div>
                      </div>
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
                          <h2>Название товара</h2>
                          <input class="input" type="text" :value="product.title" />
                      </div>
                      <div class="product__attribute">
                          <h2>Цвет</h2>
                            <div class="product__colors">
                                <label v-for="color in product.colors" class="color-checkbox">
                                  <input type="radio" :value="color.id" name="color" :checked="product.color_id == color.id" >
                                  <span class="color-checkbox__check">
                                    <span class="color-checkbox__check-color" :style="{'backgroundColor' : color.value}"></span>
                                  </span>
                                </label>
                            </div>
                      </div>
                      <input class="product__additional-attributes-trigger" type="checkbox" id="additional-attributes" /><label for="additional-attributes"><span>Раскрыть дополнительные поля</span></label>
                      <div class="product__additional-attributes">
                        <div v-for="characteristic in characteristics" class="product__attribute product__attribute--short">
                            <h2>{{ characteristic.characteristic_name }}</h2>
                            <select v-if="characteristic.type == 4" class="select" :value="characteristic.value">
                              <option v-for="val in characteristic.available_values" :selected="(val.id == characteristic.value)" :value="val.id">{{val.title}}</option>
                            </select>
                            <input v-if="characteristic.type == 1 && !Array.isArray(characteristic.value)" type="text" class="input" :value="characteristic.value"/>
                            <div v-if="characteristic.type == 1 && Array.isArray(characteristic.value)" class="multiple-input">
                              <div class="multiple-input">
                                <div v-for="value in characteristic.value" class="multiple-input__item">
                                  <input type="text" class="input input--multiple" :value="value"/>
                                  <div class="multiple-input__del">
                                    <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="19px" height="17px">
                                    <path fill-rule="evenodd"  fill="currentColor"
                                    d="M18.230,4.736 L17.308,4.736 L15.96,16.370 C15.9,16.757 14.713,17.2 14.325,17.2 L4.674,17.2 C4.286,17.2 3.990,16.757 3.901,16.362 L1.691,4.736 L0.768,4.736 C0.338,4.736 0.1,4.406 0.1,3.983 C0.1,3.561 0.338,3.231 0.768,3.231 L6.361,3.231 L6.361,2.67 C6.361,0.927 7.306,0.0 8.467,0.0 L10.476,0.0 C11.637,0.0 12.582,0.927 12.582,2.67 L12.582,3.231 L18.175,3.231 C18.590,3.231 18.998,3.603 18.998,3.983 C18.998,4.406 18.661,4.736 18.230,4.736 ZM11.48,2.67 C11.48,1.787 10.791,1.560 10.476,1.560 L8.467,1.560 C8.152,1.560 7.895,1.787 7.895,2.67 L7.895,3.231 L11.48,3.231 L11.48,2.67 ZM3.272,4.736 L5.313,15.496 L13.629,15.496 L15.728,4.736 L3.272,4.736 ZM11.424,13.59 L11.368,13.59 C11.172,13.59 10.981,12.974 10.844,12.826 C10.704,12.674 10.636,12.473 10.656,12.275 L10.936,7.501 C10.977,7.97 11.291,6.785 11.665,6.785 C11.688,6.785 11.712,6.786 11.735,6.788 L11.747,6.790 L11.759,6.790 C11.955,6.790 12.146,6.875 12.283,7.23 C12.423,7.175 12.491,7.376 12.471,7.574 L12.192,12.348 C12.191,12.766 11.869,13.59 11.424,13.59 ZM7.562,13.59 L7.519,13.59 C7.123,13.59 6.793,12.749 6.751,12.338 L6.473,7.584 C6.427,7.132 6.811,6.828 7.208,6.788 C7.232,6.786 7.255,6.785 7.278,6.785 C7.652,6.785 7.966,7.97 8.8,7.511 L8.286,12.265 C8.312,12.521 8.202,12.696 8.105,12.798 C7.970,12.941 7.773,13.36 7.562,13.59 Z"/>
                                    </svg>
                                  </div>
                                </div>
                              </div>
                              <button class="btn btn--secondary multiple-input__add">Добавить значение</button>
                            </div>
                            <input v-if="characteristic.type == 2" type="number" class="input input--number" :value="characteristic.value"/>
                            <label v-if="characteristic.type == 3" class="boolean">
                              <input type="checkbox" name="" :value="characteristic.value" :checked="characteristic.value">
                              <span class="boolean__check"></span>
                            </label>
                        </div>
                      </div>
                      <div class="product__images">
                          <div class="product__attribute">
                              <h2>Фото товара <span>Рекомендуемый размер <br>фото — 1000х1000 px. </span><span>Вы можете загрузить до 8 фотографий.</span></h2>
                              <div class="product__images-wrap">
                                  <div class="product__images-nav"><button class="product__images-arrow product__images-arrow--up disabled" data-shift="-1"></button>
                                      <div class="product__images-list product__images-list--slider">
                                          <div class="product__images-track" data-shift="0" data-viewed="5">
                                              <div v-if="product.images">
                                                <div class="product-small-img" v-for="(image, index) in product.images" :class="{ 'active' : index == 0 }">
                                                  <img :src="imgPath + image" />
                                                </div>
                                              </div>
                                          </div>
                                      </div><button class="product__images-arrow product__images-arrow--down" data-shift="1"></button>
                                  </div>
                                  <div class="product__images-selected">
                                      <div class="product__images-empty">Не загружено ни одной фотографии.<br>Загрузите хотя бы одну.</div><img :src="product.images ? (imgPath + product.images[0]) : ''" />
                                  </div>
                                  <div class="product__images-controls">
                                      <div class="product__images-control product__images-control--add"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15px" height="15px">
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M1.499,5.999 L13.499,5.999 C14.328,5.999 14.999,6.671 14.999,7.499 C14.999,8.328 14.328,8.999 13.499,8.999 L1.499,8.999 C0.671,8.999 0.0,8.328 0.0,7.499 C0.0,6.671 0.671,5.999 1.499,5.999 Z" />
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.499,0.0 C8.328,0.0 8.999,0.671 8.999,1.499 L8.999,13.499 C8.999,14.328 8.328,14.999 7.499,14.999 C6.671,14.999 5.999,14.328 5.999,13.499 L5.999,1.499 C5.999,0.671 6.671,0.0 7.499,0.0 Z" />
                                          </svg>
                                          <span>добавить фото</span>
                                      </div>
                                      <div class="product__images-control product__images-control--up"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="10px">
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M10.532,0.431 L17.567,7.436 C18.149,8.16 18.149,8.957 17.567,9.537 C16.984,10.118 16.39,10.118 15.456,9.537 L8.422,2.532 C7.839,1.952 7.839,1.11 8.422,0.431 C9.5,0.149 9.949,0.149 10.532,0.431 Z" />
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M2.574,9.537 L9.608,2.532 C10.191,1.952 10.191,1.11 9.608,0.431 C9.25,0.149 8.80,0.149 7.498,0.431 L0.463,7.436 C0.118,8.16 0.118,8.957 0.463,9.537 C1.46,10.118 1.991,10.118 2.574,9.537 Z" />
                                          </svg>
                                          <span>поднять вверх</span>
                                      </div>
                                      <div class="product__images-control product__images-control--down"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="10px">
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M10.532,0.431 L17.567,7.436 C18.149,8.16 18.149,8.957 17.567,9.537 C16.984,10.118 16.39,10.118 15.456,9.537 L8.422,2.532 C7.839,1.952 7.839,1.11 8.422,0.431 C9.5,0.149 9.949,0.149 10.532,0.431 Z" />
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M2.574,9.537 L9.608,2.532 C10.191,1.952 10.191,1.11 9.608,0.431 C9.25,0.149 8.80,0.149 7.498,0.431 L0.463,7.436 C0.118,8.16 0.118,8.957 0.463,9.537 C1.46,10.118 1.991,10.118 2.574,9.537 Z" />
                                          </svg>
                                          <span>опустить вниз</span>
                                      </div>
                                      <div class="product__images-control product__images-control--del"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px">
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M3.317,1.196 L12.803,10.681 C13.388,11.267 13.388,12.217 12.803,12.803 C12.217,13.389 11.267,13.389 10.681,12.803 L1.196,3.317 C0.610,2.732 0.610,1.782 1.196,1.196 C1.782,0.610 2.732,0.610 3.317,1.196 Z" />
                                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M12.803,1.196 C13.389,1.782 13.389,2.732 12.803,3.317 L3.317,12.803 C2.732,13.388 1.782,13.388 1.196,12.803 C0.610,12.217 0.610,11.267 1.196,10.681 L10.681,1.196 C11.267,0.610 12.217,0.610 12.803,1.196 Z" />
                                          </svg>
                                          <span>удалить фото</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="product__save-back">
                        <router-link :to="$route.meta.back_route" class="btn btn--secondary">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="8px" height="13px">
                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M0.903,4.974 L4.974,0.903 C5.560,0.317 6.510,0.317 7.96,0.903 C7.681,1.489 7.681,2.439 7.96,3.25 L3.25,7.96 C2.439,7.681 1.489,7.681 0.903,7.96 C0.318,6.510 0.318,5.560 0.903,4.974 Z" />
                              <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.96,9.974 L3.25,5.903 C2.439,5.317 1.489,5.317 0.903,5.903 C0.318,6.489 0.318,7.439 0.903,8.25 L4.974,12.96 C5.560,12.681 6.510,12.681 7.96,12.96 C7.681,11.510 7.681,10.560 7.96,9.974 Z" />
                          </svg>
                          <span>Вернуться</span>
                        </router-link>
                        <button class="btn btn--primary">Сохранить изменения</button>
                      </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот товар</div>
            </div>`,
  data: function () {
    return {
      imgPath: productImgPath,
      editable: true,
      categories: [],
      categoriesFlat: [],
      categorySearch: '',
      selectedCategoryId: '',
      selectedCategoryName: '',
      brands: [],
      brandSearch: '',
      selectedBrandId: '',
      selectedBrandName: '',
      countries: [],
      countrySearch: '',
      selectedCountryId: '',
      selectedCountryName: '',
      product: {}
    }
  },
  computed: {
    characteristics(){
      if (!this.product.characteristics) return false;
      let characteristics = this.product.characteristics;
      characteristics = characteristics.filter((characteristic) => {
        return (characteristic.type != 0 && characteristic.id != "000000001" && characteristic.id != "000000002" && characteristic.id != "000000003" && characteristic.id != "000000004")
      })
      console.log(characteristics);
      return characteristics;
    },
    filteredCategories(){
      if (this.categorySearch.length < 3) return false;
      let categories = this.categoriesFlat;
      categories = categories.filter((category) => {
        return (category.name.toLowerCase().includes(this.categorySearch.toLowerCase()))
      })
      return categories;
    },
    filteredBrands(){
      if (this.brandSearch.length < 2) return false;
      let brands = this.brands;
      brands = brands.filter((brand) => {
        return (brand.title.toLowerCase().includes(this.brandSearch.toLowerCase()))
      })
      return brands;
    },
    filteredCountries(){
      if (this.countrySearch.length < 2) return false;
      let countries = this.countries;
      countries = countries.filter((country) => {
        return (country.title.toLowerCase().includes(this.countrySearch.toLowerCase()))
      })
      return countries;
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
    },
    getProduct() {
      let requestUrl = '/control-panel/edit-product';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            product_id : this.$route.params.id
          }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.categories = response.data.category_tree;
              this.product = response.data.product;
              this.brandSearch = this.product.brand_name;
              this.selectedBrandId = this.product.brand_id;
              this.selectedBrandName = this.product.brand_name;
              this.brands = this.product.brands;
              this.countrySearch = this.product.country_name;
              this.selectedCountryId = this.product.country_id;
              this.selectedCountryName = this.product.country_name;
              this.countries = this.product.countries;
              this.flatCategories();
              console.log(this.product);
            }
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.editable = false;
              $('.main__loader').hide();
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
        this.brandSearch = this.brandName;
      }
    },
    checkCountry() {
      if (!this.countrySearch){
        this.countrySearch = this.countryName;
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
    checkProductImagesSlider();
    $('.main__loader').hide();
  }
}

const StoreEdit = { template: '<div>Магазин с id {{ $route.params.id }}</div>' }

const ApiIntegration = {
  template: '<div><div v-html="htmlcontent"></div></div>',
  data: function () {
    return {
      htmlcontent: ''
    }
  },
  methods: {
    getContent() {
      let requestUrl = '/control-panel/api-integration';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
          .post(requestUrl,
            Qs.stringify({
              page : this.$route.name
            }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.htmlcontent = response.data.data;
            }
          })
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getContent();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}

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

const Testimonials = {
  template: '<div>Отзывы</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Instructions = {
  template: '<div>Инструкции</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Documents = {
  template: '<div>Документы</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Support = {
  template: '<div>Поддержка</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const Settings = {
  template: '<div>Управление аккаунтом</div>',
  mounted: function(){
    $('.main__loader').hide();
  }
}

const routes = [
  {
    name: 'orders',
    path: '/orders',
    meta: {
      h1: 'Заказы и возвраты'
    },
    component: Orders
  },
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
  },
  {
    name: 'api',
    path: '/api-integration',
    meta: {
      h1: 'Интеграция API'
    },
    component: ApiIntegration
  },
  {
    name: 'price-list',
    path: '/price-list',
    meta: {
      h1: 'Цены и скидки'
    },
    component: PriceList
  },
  {
    name: 'testimonials',
    path: '/testimonials',
    meta: {
      h1: 'Отзывы'
    },
    component: Testimonials
  },
  {
    name: 'instructions',
    path: '/instructions',
    meta: {
      h1: 'Инструкции'
    },
    component: Instructions
  },
  {
    name: 'documents',
    path: '/documents',
    meta: {
      h1: 'Документы'
    },
    component: Documents
  },
  {
    name: 'support',
    path: '/support',
    meta: {
      h1: 'Поддержка'
    },
    component: Support
  },
  {
    name: 'settings',
    path: '/settings',
    meta: {
      h1: 'Управление аккаунтом'
    },
    component: Support
  }
]

const router = new VueRouter({
  routes,
  //mode: 'history'
})

const cp = new Vue({
  router,
  mounted: function(){
    if(!this.$route.name) router.replace('/orders');
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

$(document).on('click','.multiple-input__del',function(){
  if ($(this).parent().parent().find('.multiple-input__item').length > 1){
    $(this).parent().remove();
  } else {
    $(this).parent().find('input').val('');
  }
});
$(document).on('click','.multiple-input__add',function(){
  $(this).parent().find('.multiple-input__item').eq(0).clone().appendTo($(this).parent().find('.multiple-input')).val('');
});
