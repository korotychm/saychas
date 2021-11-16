const ProductAddFile = {
  template: `<div>
                <div class="filter">
                  <div class="filter__btn">
                      <router-link to="/product-add" class="btn btn--secondary">По одному товару</router-link>
                  </div>
                  <div class="filter__btn">
                      <a class="btn btn--primary">Массой товаров</a>
                  </div>
                  <div class="filter__btn" style="display: none;">
                      <router-link to="/product-add-api" class="btn btn--secondary">Загрузка по API</router-link>
                  </div>
                </div>
                <div class="cp-container product product-add-file">
                  <div class="product__category">
                      <h2>Категория <span class="required">*</span></h2>
                      <div class="search-select">
                          <input class="input search-select__input" type="text" value="selectedCategoryName" v-model="categorySearch" @focusout="checkCategory()" />
                          <div class="search-select__suggestions">
                              <div v-if="!categorySearch" class="search-select__empty">Начните вводить название категории для поиска</div>
                              <div v-if="(categorySearch && !filteredCategories.length)" class="search-select__empty">Ничего не найдено</div>
                              <div v-if="(categorySearch && filteredCategories.length)">
                                <label v-for="category in filteredCategories">
                                  <input type="radio" name="suggest" :checked="(category.id == selectedCategoryId)" />
                                  <span class="search-select__suggestion" @mousedown="selectCategory(category.id, category.name)">
                                    <span class="search-select__suggestion-category--parent">{{category.parent}}</span>
                                    <span class="search-select__suggestion-category">{{category.name}}</span>
                                  </span>
                                </label>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="product-add-file__files" v-if="file">
                    <div class="product-add-file__files-download">
                      <a href="" :download="filePathMoz" v-if="checkBrowser">Скачать файл</a>
                      <a :href="filePath" :download="fileName" v-else>Скачать файл</a>
                      <p>Скачайте и заполните файл.</p>
                      <p>Чем больше полей заполните - тем легче пользователям будет найти ваш товар.</p>
                    </div>
                    <div class="product-add-file__files-upload">
                      <input type="file" id="upload-file" @change="uploadFile"/>
                      <label for="upload-file">Загрузить файл</label>
                      <p>Загрузите заполненный файл.</p>
                      <p>Товары появятся на сайте после обработки.</p>
                    </div>
                  </div>
                     <div v-if="fileUploaded" class="reload-result">
                        <div class="result__container">
                        
                        </div>
                        <div class="result-btn__container">
                          <button class="btn btn--primary">Обновить результат</button>
                        </div>
                    </div>
                </div>
            </div>`,
  data () {
    return {
      categories: [],
      categoriesFlat: [],
      categorySearch: '',
      selectedCategoryId: '',
      selectedCategoryName: '',
      file: false,
      filePath: '',
      filePathMoz: '',
      fileName: '',
      fileUploaded: false,
    }
  },
  computed: {
    filteredCategories(){
      if (this.categorySearch == '') return false;
      let categories = this.categoriesFlat;
      categories = categories.filter((category) => {
        return (category.name.toLowerCase().includes(this.categorySearch.toLowerCase()))
      })
      return categories;
    },
    removeSting() {
      if(this.checkBrowser) {
        return this.filePath.replace(window.location.href, '')
      } else {
        return this.filePath ? this.filePath : ''
      }
    },
    checkBrowser () {
      if (navigator.userAgent.toLowerCase().includes('firefox')) {
        return true;
      }
      return false;
    },
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
              $('.main__loader').hide();
            }
          });
    },
    checkCategory() {
      setTimeout(() => {
        if (this.categorySearch != this.selectedCategoryName){
          this.categorySearch = this.selectedCategoryName;
        }
      }, 100);
    },
    selectCategory(id,value) {
      if (id != this.selectedCategoryId){
        this.selectedCategoryId = id;
        this.categorySearch = value;
        this.selectedCategoryName = value;
        this.getFile(id);
      }
    },
    async uploadFile () {
      let file  = document.querySelector("#upload-file").files
      let formData = new FormData()
      formData.append('file', file[0]);
      await axios.post('/control-panel/upload-product-file', formData, {
        headers: {'Content-Type': 'multipart/form-data'}
      }).then(response => {
        showMessage('Файл загружен, ожидайте ответа')
        this.fileUploaded = true;
        console.log(response)
      })
    },
    async getFile(id) {
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let requestUrl = '/control-panel/get-product-file';
      await axios
        .post(requestUrl,Qs.stringify({
          data: {
            category_id: this.selectedCategoryId,
            query_type: 'product'
          }
        }),{headers})
          .then(response => {
            this.filePath = window.location.href + '/' + productsDocumentPath + response.data.filename;
            this.fileName = response.data.filename;
            this.filePathMoz = response.data.filename;
            // this.filePathMoz = '/' + response.data.filename.replace(/^_/,'');
            // console.log(this.filePath)
            // console.log('Файл',response);
          })
          .catch(error => {
            if (error.response.status == '403'){
              location.reload();
            }
          });
      this.file = true;
    },
  },
  created: function(){
    $('.main__loader').show();
    this.getCategories();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}
