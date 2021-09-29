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
        </div>
        <!-- фильтр -->
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
        selectedFilters: {
          category_id: '',
          store_id: ''
        },
        search: '',
        filtersCreated: false
      }
  },
  methods: {
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
              console.log(response.data);
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
