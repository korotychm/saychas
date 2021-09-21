const StoresMap = {
  template: `<div>
    <div v-if="htmlContent" v-html="htmlContent"></div>
    <div v-else>
      <div class="filter">
        <div style="display: flex; justify-content: space-between; width: 100%">
          <div style="display: flex;">
            <div class="filter__btn">
              <router-link to="/stores" class="btn btn--secondary">Списком</router-link>
            </div>
            <div class="filter__btn">
              <a class="btn btn--primary">На карте</a>
            </div>
            <div class="filter__select">
              <div class="custom-select custom-select--radio">
                <div class="custom-select__label input">Все статусы</div>
                <div class="custom-select__dropdown">
                  <label v-for="(val,idx) in characteristic.available_values" class="custom-select__option">
                    <input type="radio" checked="checked" value="" name="status_filter" v-model="selectedFilters.status_id" @change="loadPage()" />
                    <span>Все статусы</span>
                  </label>
                  <label v-for="status in filters.statuses" class="custom-select__option">
                    <input type="radio" :checked="(status[0] === selectedFilters.status_id)" :value="status[0]" name="status_filter" v-model="selectedFilters.status_id" @change="loadPage()" />
                    <span>{{status[1]}}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="filter__btn">
            <router-link to="/store-add" class="btn btn--primary">+ Добавить магазин</router-link>
          </div>
        </div>
      </div>
      <div class="cp-container stores-map">
        <div style="height: 600px">
          <yandex-map :settings="settings" :coords="coords" zoom="10" :controls="controls">
            <div v-for="(store, idx) in stores">
              <ymap-marker :markerId="store.id" marker-type="Placemark" :coords="[store.geox,store.geoy]" :balloon-template="balloonTemplate(idx)" :icon="markerIcon(store.status_id)"></ymap-marker>
            </div>
          </yandex-map>
        </div>
      </div>
    </div>
  </div>`,
  data: function () {
    return {
      htmlContent: '',
      page_no: 1,
      rows_per_page: 500,
      stores: [],
      pages: 1,
      filters: {},
      selectedFilters: {
        status_id: ''
      },
      search: '',
      filtersCreated: false,
      responsedata: '',
      settings: {
        apiKey: '',
        lang: 'ru_RU',
        coordorder: 'latlong',
        version: '2.1'
      },
      coords: [55.753215,37.622504],
      controls: ['fullscreenControl','zoomControl']
    }
  },
  methods: {
    markerIcon(status) {
      return {
        layout: 'default#image',
        imageHref: '/img/icons/map-marker-' + status + '.svg',
        imageSize: [34, 42],
        imageOffset: [-17, -21]
      }
    },
    balloonTemplate(idx) {
      console.log('Индекс',idx);
      return `
        <div class="stores-map__baloon">
          <h3>${this.stores[idx].title}</h3>
          <p>${this.stores[idx].address}</p>
          <p><a href="/control-panel#/stores/${this.stores[idx].id}">Редактировать</a></p>
        </div>
      `
    },
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
