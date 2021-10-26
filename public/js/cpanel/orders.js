const Orders = {
  template:
    `<div>
      <div v-if="htmlContent" v-html="htmlContent"></div>
      <div v-else>
          <div class="filter">
              <div class="btn-switcher">
                  <div class="btn-switcher__btn active">Заказы</div>
                  <div class="btn-switcher__btn">Возвраты</div>
              </div>
              <div class="filter__select filter__select--store">
                  <div class="custom-select custom-select--radio">
                      <div class="custom-select__label input">Все магазины<div class="custom-select__dropdown">
                              <div class="custom-select__dropdown-inner"><label class="custom-select__option"><input type="radio" checked="checked" value="" name="filter-store" /><span>Все магазины</span></label></div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="filter__select filter__select--status">
                  <div class="custom-select custom-select--radio">
                      <div class="custom-select__label input">Все статусы<div class="custom-select__dropdown">
                              <div class="custom-select__dropdown-inner"><label class="custom-select__option"><input type="radio" checked="checked" value="" name="filter-status" /><span>Все статусы</span></label></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="cp-container list orders" v-if="orders2">
              <div class="thead">
                  <div class="td">Название магазина</div>
                  <div class="td">Номер заказа</div>
                  <div class="td">Дата заказа</div>
                  <div class="td">Статус</div>
              </div>
              <div class="tbody">
                <div class="tr orders__item" v-for="order in orders">
                    <div class="td orders__store">{{ order.shop }}</div>
                    <div class="td orders__order">{{ order.order_id }}</div>
                    <div class="td orders__date">{{ order.date }}</div>
                    <div class="td orders__status">{{ order.status }}</div>
                    <div class="td orders__short">
                        <div class="orders__images">
                            <div class="orders__image" v-for="product in order.items">
                              <img :src="product.image" />
                            </div>
                        </div>
                        <div class="orders__count"><b>{{ order.items.length }}</b> товара</div>
                        <div class="orders__sum">на сумму<span>{{ (order.order_sum / 100).toLocaleString() }} ₽</span></div>
                    </div>
                    <div class="td orders__btn"><button class="btn btn--primary">Приступить к сборке<span>00:00</span></button></div>
                </div>
              </div>
          </div>
      </div>
    </div>`,
    data: function () {
        return {
          htmlContent: '',
          page_no: 1,
          rows_per_page: 10,
          orders: {},
          pages: 1,
          filters: {},
          imgPath: productImgPath,
          selectedFilters: {
            status_id: '',
            store_id: ''
          },
          filtersCreated: false
        }
    },
    methods: {
      getOrders() {
        let requestUrl = '/control-panel/show-orders';
        if (this.filtersCreated) {
         requestUrl = '/control-panel/show-orders-from-cache';
        }
        const headers = { 'X-Requested-With': 'XMLHttpRequest' }
        axios
          .post(requestUrl,
            Qs.stringify({
              page_no : this.page_no,
              rows_per_page : this.rows_per_page,
              filters: this.selectedFilters,
              use_cache: this.filtersCreated
            }), {headers})
            .then(response => {
              if (response.data.data === true) {
                location.reload();
              } else {
                console.log('Response from show-orders',response.data);
                this.pages = response.data.data.limits.total;
                this.orders = response.data.data.body;
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
      }
    },
    created: function(){
      $('.main__loader').show();
      this.getOrders();
    },
    updated: function(){
      $('.main__loader').hide();
    }
}
