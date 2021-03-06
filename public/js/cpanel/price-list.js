const PriceList = {
  template:
    `<div>
      <div v-if="htmlContent" v-html="htmlContent"></div>
      <div v-else>
        <div class="filter">
          <form class="filter__search" @submit.prevent="loadPage()">
            <input class="input input--white" type="text" v-model="search" placeholder="Быстрый поиск"  :class="{'custom-disable': showFileMenu}"/>
            <button>
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" />
              </svg>
            </button>
          </form>
          <div class="filter__select" :class="{'custom-disable': showFileMenu}">
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
          <div class="filter__btn">
<!--            <a class="btn btn&#45;&#45;secondary disabled" href="#">Скачать список</a>-->
          </div>
          <div class="filter__btn">
            <button class="btn btn--primary" @click="showMenuWithAjax">Загрузить из файла</button>
          </div>

        </div>
            <div class="cp-container" v-if="showFileMenu">
                <div class="product-add-file__files">
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
                      <ul v-for="item of downloadUrls" >
                      <li><a :href="prefixer(item.result_file)" :download="item.result_file">{{item.result_file}}</a></li>
                      </ul>
                      </div>
                      <div class="result-btn__container">
                        <button class="btn btn--primary" @click="checkFiles">Обновить результат</button>
                      </div>
                  </div>
            </div>
        <div class="cp-container pricelist list" v-else>
          <div class="thead">
            <div class="td"></div>
            <div class="td">Наименование</div>
            <div class="td">Категория</div>
            <div class="td">Скидка</div>
            <div class="td">Стоимость</div>
          </div>
          <div class="tbody" v-if="products.length">
              <div v-for="(product, index) in products" class="tr pricelist__item">
                  <div class="td pricelist__img product-small-img">
                    <img :src="(product.image) ? (((product.moderated) ? imgPathModerated : imgPath) + product.image) : '/img/ui/nophoto.jpg'" />
                  </div>
                  <div class="td td--hover pricelist__title">
                    <div class="products__title">
                      {{ product.product_name }}
                      <span class="products__art">Арт.: {{ product.vendor_code }}</span>
                    </div>
                  </div>
                  <div class="td pricelist__category">
                      <div>{{ product.mother_categories[1][1] }}</div>
                      <div>{{ product.category_name }}</div>
                  </div>
                  <div class="td pricelist__discount"><span v-if="product.discount!==''">{{ product.discount }}</span><span v-else>0</span>%</div>
                  <div class="td">
                    <div v-if="+product.discount > 0" class="pricelist__oldprice">{{ Math.round(+product.price).toLocaleString() }} ₽</div>
                    <div v-if="+product.discount > 0" class="pricelist__price">{{ Math.round(product.price * (100 - product.discount) / 100).toLocaleString() }} ₽</div>
                    <div v-else class="pricelist__price">{{ Math.round(+product.price) }} ₽</div>
                  </div>
                  <div class="pricelist__popup">
                    <div class="pricelist__popup-category">{{ product.category_name }}</div>
                    <div class="pricelist__popup-inputs">
                      <div class="pricelist__popup-input-group">
                        <div class="pricelist__popup-sale">
                          <input type="number" max="100" min="0" v-model.lazy="product.discount" @change="checkDiscount(index)" />
                        </div>
                        <div class="pricelist__popup-price">
                          <input type="number" min="0" max="999999" length="6" v-model.lazy="product.price" @change="checkPrice(index)" />
                        </div>
                      </div>
                      <p>Изменение цен и скидок происходит раз в сутки - в 03:00</p>
                    </div>
                    <div class="pricelist__popup-right">
                      <div class="pricelist__popup-total">
                        <p>Итого<br> с учетом скидки</p>
                        <h3 v-if="product.discount">{{ Math.round(+product.price * (100 - +product.discount) / 100).toLocaleString() }} ₽</h3>
                        <h3 v-else> {{ Math.round(+product.price).toLocaleString() }} ₽</h3>
                      </div>
                      <button class="btn btn--primary" @click="saveProduct(index)">Применить</button>
                    </div>
                  </div>
              </div>
          </div>
        </div>
        <div class="pagination">
          <a v-if="pages > 10" @click="loadPage(page_no - 1)" :class="{'custom-disable': page_no <= 1}"><</a>
          <a v-for="index in paginator" :class="{active : (index == page_no), 'dot-disable': (index === '...')}" @click="loadPage(index)">{{ index }}</a>
          <a v-if="pages > 10" @click="loadPage(page_no + 1)" :class="{'custom-disable': page_no >= pages}">></a>
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
          category_id: ''
        },
        search: '',
        filtersCreated: false,
        showFileMenu: false,
        fileName: '',
        filePath: '',
        fileUploaded: false,
        downloadFileName: '',
        intermediatePath: '',
          downloadUrls: []
      }
  },
  computed: {
    paginator () {
      let promt = []
      let result = []
      if (this.pages > 10) {
        for (let i = 1; i <= this.pages; i++) {
          promt.push(i)
        }
        if (this.page_no <= 4) {
            result.push(...promt.slice(0, 5))
            result.push('...')
            result.push(...promt.slice(-1))
        } else if (this.page_no >= promt.length - 3) {
            result.push(...promt.slice(0, 1))
            result.push('...')
            result.push(...promt.slice(-5))
        } else {
            result.push(...promt.slice(0, 1))
            result.push('...')
            result.push(...promt.filter(item => {
              console.log(item)
              return item === this.page_no -2 || item === this.page_no -1 || item === this.page_no || item === this.page_no + 1 || item === this.page_no + 2
            }))
            result.push('...')
            result.push(...promt.slice(-1))
        }
        return result
      }
      return this.pages
    }
  },
  methods: {
      prefixer (item) {
          return this.intermediatePath + item
      },
      async uploadFile () {
          let file  = document.querySelector("#upload-file").files
          let formData = new FormData()
          formData.append('file', file[0]);
          formData.append( 'query_type', 'price')
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
              .post(requestUrl, Qs.stringify({
                  data: {
                      query_type: 'price',
                  }
              }), {headers})
              .then(response => {
                  this.downloadUrls = response.data.urls
                  // this.downloadUrls.forEach(item =>  {
                  //   item = location.origin + item;
                  // })
              })
      },
    async showMenuWithAjax () {
      const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Content-Disposition': 'attachment' };
      let requestUrl = '/control-panel/get-product-file';
        if (this.filePath) {
            this.showFileMenu = !this.showFileMenu
            return;
        }
      await axios
          .post(requestUrl,Qs.stringify({
            data: {
              // category_id: '',
              // provider_id: 'provider_id',
              query_type: 'price'
            }
          }),{headers})
          .then(response => {
              this.fileName = response.data.filename;
              this.fileName = this.fileName.substr(this.fileName.indexOf('/P_') + 0)
              this.filePath = '/documents' + this.fileName;
              this.downloadFileName = this.fileName.split('/').pop()
              this.intermediatePath = this.filePath.replace(this.fileName.split('/').pop(), '')
              this.checkFiles();
          })
          .catch(error => {
            console.log(error)
            // if (error.response.status == '403'){
            //   location.reload();
            // }
          });
      this.showFileMenu = !this.showFileMenu
    },
    checkDiscount(index) {
      if (this.products[index].discount === ''){
        this.products[index].discount = 0;
      }
      this.products[index].discount = parseInt(this.products[index].discount);
      if (this.products[index].discount > 100){
        this.products[index].discount = 100;
      }
    },
    checkPrice(index) {
      if (this.products[index].price === ''){
        this.products[index].price = 0;
      }
      this.products[index].price = parseInt(this.products[index].price);
      if (this.products[index].price > 999999){
        this.products[index].price = 999999;
      }
    },
    setRubPrice() {
      for (product of this.products) {
        product.price = product.price / 100;
      }
    },
    saveProduct(index) {
      let requestUrl = '/control-panel/update-price-and-discount';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      let request = JSON.parse(JSON.stringify(this.products[index]));
      request.price = request.price * 100;
      console.log('Запрос на сохранение цены', request);
      if (!request.price){
        showServicePopupWindow('Невозможно сохранить изменения', 'Пожалуйста, заполните цену товара');
      } else {
        axios
          .post(requestUrl,
            Qs.stringify({
              data: {
                product : JSON.stringify(request)
              }
            }),
            {
              headers
            })
            .then(response => {
              console.log('Ответ на сохранение цены', response.data);
              if (response.data.result){
                showMessage('Цена товара изменена и обновится на сайте сегодня ночью!');
                $('.pricelist__item').removeClass('active');
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
    getProducts() {
      let requestUrl = '/control-panel/show-price-and-discount';
      if (this.filtersCreated) {
       requestUrl = '/control-panel/show-price-and-discount-from-cache';
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
              console.log('Response from show-price-and-discount',response.data);
              this.pages = response.data.data.limits.total;
              this.products = response.data.data.body;
              console.log('Получено',this.products);
              this.setRubPrice();
              console.log('Обработано',this.products);
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

$(document).on('click','.pricelist__item, .inventory__item',function(){
  $('.pricelist__item, .inventory__item').removeClass('active');
  $(this).addClass('active');
});

$(document).mouseup(function(e)
{
    var container = $(".pricelist__popup");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.parent().removeClass('active');
    }
});

$(document).mouseup(function(e)
{
    var container = $(".inventory__popup");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.parent().removeClass('active');
    }
});
