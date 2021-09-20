const Stores = {
  template: `<div>
    <div v-if="htmlContent" v-html="htmlContent"></div>
    <div v-else>
      <div class="filter">
        <div style="display: flex; justify-content: space-between; width: 100%">
          <div style="display: flex;">
            <div class="filter__btn">
              <a class="btn btn--primary">Списком</a>
            </div>
            <div class="filter__btn">
              <router-link to="/stores-map" class="btn btn--secondary">На карте</router-link>
            </div>
            <div class="filter__select">
              <select class="select select--white" v-model="selectedFilters.status_id" value="" @change="loadPage()">
                <option value="" selected >Все статусы</option>
                <option v-for="status in filters.statuses" :value="status[0]">{{ status[1] }}</option>
              </select>
            </div>
          </div>
          <div class="filter__btn">
            <router-link to="/store-add" class="btn btn--primary">+ Добавить магазин</router-link>
          </div>
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
