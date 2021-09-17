const ProductAdd = {
  template: `<div>
                <div class="filter">
                  <div class="filter__btn">
                      <a class="btn btn--primary">По одному товару</a>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-file" class="btn btn--secondary">Массой товаров</router-link>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-api" class="btn btn--secondary">Загрузка по API</router-link>
                  </div>
                </div>
                <div class="cp-container product">
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
                  <div v-if="product.category_id" class="product__info">
                    <div class="product__main-attributes">
                      <div class="product__attribute  product__attribute--short">
                          <h2>Артикул</h2>
                          <input class="input" type="text" v-model="product.vendor_code" />
                      </div>
                      <div v-if="(product.country_id !== undefined)" class="product__attribute  product__attribute--short">
                          <h2>Страна производства</h2>
                            <div class="search-select">
                                <input class="input search-select__input" type="text" value="product.country_name" v-model="countrySearch" @focusout="checkCountry()" />
                                <div class="search-select__suggestions">
                                    <div v-if="!countrySearch" class="search-select__empty">Начните вводить название страны для поиска</div>
                                    <div v-if="(countrySearch && !filteredCountries.length)" class="search-select__empty">Ничего не найдено</div>
                                    <div v-if="(countrySearch && filteredCountries.length)">
                                      <label v-for="country in filteredCountries">
                                        <input type="radio" name="suggest" :checked="(country.id == selectedCountryId)" />
                                        <span class="search-select__suggestion" @click="selectCountry(country.id, country.title)">
                                          <span>{{ country.title }}</span>
                                        </span>
                                      </label>
                                    </div>
                                </div>
                            </div>
                      </div>
                      <div v-if="(product.brand_id !== undefined)" class="product__attribute product__attribute--short">
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
                          <input class="input" type="text" v-model="product.title" />
                      </div>
                      <div v-if="(product.color_id !== undefined)" class="product__attribute">
                          <h2>Цвет</h2>
                            <div class="product__colors">
                                <label v-for="color in product.colors" class="color-checkbox">
                                  <input type="radio" :value="color.id" name="color" :checked="product.color_id == color.id" v-model="product.color_id">
                                  <span class="color-checkbox__check">
                                    <span class="color-checkbox__check-color" :style="{'backgroundColor' : color.value}"></span>
                                  </span>
                                </label>
                            </div>
                      </div>
                      <div class="product__attribute">
                          <h2>Описание товара</h2>
                          <textarea class="textarea" v-model="product.description"></textarea>
                      </div>
                    </div>
                </div>
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
      product: {},
      currentImg : '',
      deleteImages: []
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
    },
    getCategories() {
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let requestUrl = '/control-panel/add-product';
      axios
        .post(requestUrl,'',{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.categories = response.data.category_tree;
              console.log(this.categories);
              this.flatCategories();
            }
          });
    },
    checkCategory() {
      if (!this.categorySearch){
        this.categorySearch = this.selectedCategoryName;
      }
    },
    selectCategory(id,value) {
      if (id != this.selectedCategoryId){
        let oldCategory = this.selectedCategoryId;
        this.selectedCategoryId = id;
        this.categorySearch = value;
        this.selectedCategoryName = value;
        if (oldCategory) {
          //this.saveProduct(true, oldCategory);
        } else {
          this.getCharacteristics(id);
        }
      }
    },
    getCharacteristics(id) {
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let requestUrl = '/control-panel/request-category-characteristics-only';
      axios
        .post(requestUrl,Qs.stringify({
          data: {
            category_id: this.selectedCategoryId
          }
        }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              console.log(response);
              this.product = response.data.answer.data.product
            }
          })
          .catch(error => {
            if (error.response.status == '403'){
              location.reload();
            }
          });
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getCategories();
  },
  mounted: function(){
    $('.main__loader').hide();
  }
}
