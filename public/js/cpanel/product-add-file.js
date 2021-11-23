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
                      <a :href="filePath" :download="downloadFileName">Скачать файл</a>
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
                     <div class="reload-result">
                        <div class="result__container">
                        <ul v-for="item of testUrls" >
                        <li><a :href="prefixer(item.result_file)" :download="item.result_file">файл</a></li>
                        </ul>
                        </div>
                        <div class="result-btn__container">
                          <button class="btn btn--primary" @click="checkFiles">Обновить результат</button>
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
      fileName: '',
      fileUploaded: false,
      downloadFileName: '',
      downloadUrls: {},
      testUrls:[
          {"result_file":"product_000000058_2021_10_19_1323.xls"},
          {"result_file":"product_000000006_2021_11_19_1605.xls"},
          {"result_file":"product_000000006_2021_11_19_1602.xls"}
      ]
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
  },
  methods: {
    prefixer (item) {
      return 'documents/P_00005/product/' + item
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
    async checkFiles () {
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let requestUrl = '/control-panel/place-download-link';
      await axios
          .get(requestUrl, {headers})
          .then(response => {
            this.downloadUrls = response.data.urls.map(item => {
              return location.origin + '/' + item;
            });
            // this.downloadUrls.forEach(item =>  {
            //   item = location.origin + item;
            // })
          })
    },
    async getFile(id) {
      const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Content-Disposition': 'attachment' };
      let requestUrl = '/control-panel/get-product-file';
      await axios
        .post(requestUrl,Qs.stringify({
          data: {
            category_id: this.selectedCategoryId,
            query_type: 'product'
          }
        }),{headers})
          .then(response => {
            this.fileName = response.data.filename;
            this.fileName = this.fileName.substr(this.fileName.indexOf('/P_') + 0)
            this.filePath = '/documents' + this.fileName;
            this.downloadFileName = this.fileName.split('/').pop()
          })
          .catch(error => {
            console.log(error)
            // if (error.response.status == '403'){
            //   location.reload();
            // }
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
