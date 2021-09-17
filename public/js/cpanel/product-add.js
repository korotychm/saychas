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
    delImg(){
      var currentIndex = this.product.images.indexOf(this.currentImg);
      var images = [...this.product.images];
      var shift = (!(currentIndex == 0));
      if ((currentIndex + 1) == images.length){
        next = currentIndex- 1;
      } else {
        next = currentIndex + 1
      }
      this.deleteImages.push(images[currentIndex]);
      this.currentImg = images[next];
      images.splice(currentIndex, 1);
      this.product.images = images;
    },
    moveImg(shift) {
      var currentIndex = this.product.images.indexOf(this.currentImg);
      var images = [...this.product.images];
      [images[currentIndex],images[currentIndex + shift]] = [images[currentIndex + shift], images[currentIndex]];
      this.product.images = images;
      checkProductImagesSlider();
    },
    uploadFile() {
      var data = new FormData();
      var imagefile = document.querySelector('#photo-upload');
      console.log(imagefile.files[0]);
      data.append('file', imagefile.files[0]);
      data.append('product_id', this.product.id);
      data.append('provider_id', this.product.provider_id);
      for (var key of data.entries()) {
        console.log(key[0] + ', ' + key[1]);
      }
      axios.post('/control-panel/upload-product-image', data, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
      })
      .then(response => {
        console.log(response)
        this.product.images.push(response.data.image_file_name);
        this.currentImg = response.data.image_file_name;
        checkProductImagesSlider();
      })
      .catch(error => {
        console.log(error.response)
      })
    },
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
    saveProduct(categoryChange = false, oldCategory = null) {
      let requestUrl = '/control-panel/update-product';
      if (categoryChange) {
        requestUrl = '/control-panel/request-category-characteristics';
      }
      console.log(requestUrl);
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let chars = JSON.parse(JSON.stringify(this.product.characteristics));
      for (characteristic of chars){
        delete characteristic.characteristic_name;
        delete characteristic.real_value;
        delete characteristic.title;
        delete characteristic.available_values;
        // Страна
        if (characteristic.id == '000000001'){
          characteristic.value = this.selectedCountryId;
        }
        // Бренд
        if (characteristic.id == '000000003'){
          characteristic.value = this.selectedBrandId;
        }
        // Цвет
        if (characteristic.id == '000000004'){
          characteristic.value = this.product.color_id;
        }
      }
      let category_in_request = this.selectedCategoryId;
      if (oldCategory) {
        category_in_request = oldCategory;
      }
      let request = {
        id : this.product.id,
        brand_id: this.selectedBrandId,
        category_id: category_in_request,
        color_id: this.product.color_id,
        provider_id: this.product.provider_id,
        country_id: this.selectedCountryId,
        description: this.product.description,
        title: this.product.title,
        characteristics: chars,
        images: this.product.images,
        vendor_code: this.product.vendor_code,
        del_images: this.deleteImages
      }
      console.log(request);
      axios
        .post(requestUrl,
          Qs.stringify({
            data: {
              new_category_id: this.selectedCategoryId,
              product : JSON.stringify(request)
            }
          }),
          {
            headers
          })
          .then(response => {
            if (categoryChange) {
              let product = response.data.answer.data.product;
              console.log(product);
              this.product.characteristics = product.characteristics;
              if (product.brand_id !== undefined){
                this.product.brand_id = product.brand_id;
                this.product.brand_name = product.brand_name;
              } else {
                delete this.product.brand_id;
                delete this.product.brand_name;
              }
              if (product.country_id !== undefined){
                this.product.country_id = product.country_id;
                this.product.country_name = product.country_name;
              } else {
                delete this.product.country_id;
                delete this.product.country_name;
              }
              if (product.color_id !== undefined){
                this.product.color_id = product.color_id;
              } else {
                delete this.product.color_id;
              }
            } else {
              if (response.data.result){
                router.replace('/products');
              }
            }
          })
          .catch(error => {
            console.log(error);
            if (error.response.status == '403'){
              this.editable = false;
              $('.main__loader').hide();
            }
          });
    },
    getCategories() {
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let requestUrl = '/control-panel/add-product';
      axios
        .post(requestUrl,{},{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.categories = response.data.category_tree;
              this.flatCategories();
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
      if (id != this.selectedCategoryId){
        let oldCategory = this.selectedCategoryId;
        this.selectedCategoryId = id;
        this.categorySearch = value;
        this.selectedCategoryName = value;
        //this.saveProduct(true, oldCategory);
      }
    },
    selectBrand(id,value) {
      this.selectedBrandId = id;
      this.brandSearch = value;
      this.selectedBrandName = value;
    },
    selectCountry(id,value) {
      this.selectedCountryId = id;
      this.countrySearch = value;
      this.selectedCountryName = value;
    },
    deleteValue(characteristicIndex,valueIndex){
      if (this.product.characteristics[characteristicIndex].value.length > 1){
        this.product.characteristics[characteristicIndex].value.splice(valueIndex, 1);
      } else {
        Vue.set(this.product.characteristics[characteristicIndex].value, 0, '')
      }
    },
    addValue(characteristicIndex){
      this.product.characteristics[characteristicIndex].value.push('');
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
