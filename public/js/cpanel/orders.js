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
                      <div class="custom-select__label input">Все магазины</div>
                      <div class="custom-select__dropdown">
                          <div class="custom-select__dropdown-inner">
                            <label class="custom-select__option">
                              <input type="radio" :checked="(selectedFilters.store_id === '')" value="" name="filter-store" v-model="selectedFilters.store_id" @change="loadPage()" /><span>Все магазины</span>
                            </label>
                            <label v-for="store in filters.stores" class="custom-select__option">
                              <input type="radio" :checked="(store.id === selectedFilters.store_id)" :value="store.id" name="filter-store" v-model="selectedFilters.store_id" @change="loadPage()" />
                              <span>{{store.title}}</span>
                            </label>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="filter__select filter__select--status">
                  <div class="custom-select custom-select--radio">
                      <div class="custom-select__label input">Все статусы</div>
                      <div class="custom-select__dropdown">
                          <div class="custom-select__dropdown-inner">
                            <label class="custom-select__option">
                              <input type="radio" :checked="(selectedFilters.status_id === '')" value="" name="filter-status" v-model="selectedFilters.status_id" @change="loadPage()" /><span>Все статусы</span>
                            </label>
                            <label v-for="status in filters.statuses" class="custom-select__option">
                              <input type="radio" :checked="(status[0] === selectedFilters.status_id)" :value="status[0]" name="filter-status" v-model="selectedFilters.status_id" @change="loadPage()" />
                              <span>{{status[1]}}</span>
                            </label>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="cp-container list orders" v-if="orders">
              <div class="thead">
                  <div class="td">Название магазина</div>
                  <div class="td">Номер заказа</div>
                  <div class="td">Дата заказа</div>
                  <div class="td">Статус</div>
              </div>
              <div class="tbody">
                <div class="tr orders__item" v-for="(order,index) in orders">
                    <div class="td orders__store">{{ order.store }}</div>
                    <div class="td orders__order">{{ +order.id }}</div>
                    <div class="td orders__date">{{ localeDate(order.date) }}</div>
                    <div class="td orders__status">{{ order.status }}</div>
                    <div class="td orders__short" v-show="activeOrder !== order.id && order.status_id != '02'">
                        <div class="orders__images" v-if="order.items.length">
                            <div class="orders__image" :class="{'orders__image--disabled': product.qty_partner == 0}" v-for="product in order.items">
                              <img :src="imgPath + product.image_id" />
                            </div>
                        </div>
                        <div class="orders__count">
                          <div class="orders__count-initial" v-if="order.items.length > countProducts(index)"><b>{{ order.items.length }}</b></div>
                          <div class="orders__count-actual"><b>{{ countProducts(index) }}</b> {{ getUnit(countProducts(index)) }}</div>
                        </div>
                        <div class="orders__sum">на сумму<span>{{ order.requisition_sum.toLocaleString() }} ₽</span></div>
                    </div>
                    <div class="td orders__full" v-show="activeOrder === order.id || order.status_id == '02'">
                        <div class="orders__product" v-for="product in order.items" :class="{'orders__product--zero' : (product.qty_partner == 0)}">
                            <div class="orders__product-image">
                              <img :src="imgPath + product.image_id" />
                            </div>
                            <div class="orders__product-title">{{ product.product }}</div>
                            <div class="orders__product-id">{{ product.product_id }}</div>
                            <div class="orders__product-count" v-if="order.status_id == '02'">
                                <div class="orders__product-count-initial" v-if="product.qty_partner < product.qty"><b>{{ product.qty }}</b> шт</div>
                                <div class="orders__product-count-actual"><b>{{ product.qty_partner }}</b> шт</div>
                                <div class="orders__product-count-edit" v-show="product.product_id === activeItem">
                                  <input type="number" v-model="currentQuantity" @input="if (currentQuantity > product.qty) currentQuantity = product.qty">
                                </div>
                            </div>

                            <div class="orders__product-count" v-if="order.status_id != '02' && product.qty_partner < product.qty">
                              <div class="orders__product-count-initial"><b>{{ product.qty }}</b> шт</div>
                            </div>
                            <div class="orders__product-actual" v-if="order.status_id != '02' && product.qty_partner < product.qty"><b>{{ product.qty_partner }}</b> шт</div>
                            <div class="orders__product-count" v-if="order.status_id != '02' && product.qty_partner == product.qty"><b>{{ product.qty_partner }}</b> шт</div>

                            <div class="orders__product-sum">{{ calculatePrice(product.price, product.discount).toLocaleString() }} ₽</div>
                            <div class="orders__product-edit" v-if="order.status_id == '02'">
                              <button v-if="product.product_id !== activeItem" @click="activeItem = product.product_id; currentQuantity = product.qty_partner">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px">
                                  <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M13.352,4.166 L12.750,4.778 C12.591,4.938 12.334,4.938 12.175,4.778 L9.291,1.858 C9.133,1.699 9.133,1.440 9.291,1.280 L9.291,1.280 L9.901,0.663 C10.766,0.214 12.173,0.221 13.46,0.648 C13.51,0.653 13.56,0.659 13.61,0.663 L13.64,0.663 L13.352,0.955 C14.225,1.845 14.225,3.277 13.352,4.166 ZM11.312,6.241 L5.123,12.523 C4.715,12.938 4.199,13.226 3.635,13.355 L0.735,14.12 C0.404,14.83 0.78,13.871 0.7,13.537 C0.13,13.438 0.10,13.335 0.17,13.237 L0.842,10.394 C0.986,9.900 1.250,9.449 1.610,9.83 L7.860,2.741 C8.19,2.581 8.276,2.581 8.435,2.741 L11.312,5.662 C11.469,5.822 11.469,6.80 11.312,6.241 Z" />
                                </svg>
                              </button>
                              <button class="done" v-if="product.product_id === activeItem" @click="product.qty_partner = currentQuantity; activeItem = null;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="22px" height="15px">
                                  <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M3.343,6.720 L8.778,12.155 C9.363,12.741 9.363,13.691 8.778,14.276 C8.192,14.862 7.242,14.862 6.656,14.276 L1.221,8.842 C0.636,8.255 0.636,7.306 1.221,6.720 C1.807,6.134 2.757,6.134 3.343,6.720 Z"></path>
                                  <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M20.571,2.487 L9.519,13.540 C8.950,14.109 8.27,14.109 7.458,13.540 C6.889,12.970 6.889,12.47 7.458,11.479 L18.510,0.426 C19.80,0.142 20.2,0.142 20.571,0.426 C21.140,0.995 21.140,1.918 20.571,2.487 Z"></path>
                                </svg>
                              </button>
                            </div>
                        </div>
                    </div>
                    <div class="td orders__btn" v-if="order.status_id == '01'">
                      <button class="btn btn--primary" @click="saveOrder(index,'02')">Приступить к сборке<span v-if="order.deadline">{{ order.deadline }}</span></button>
                    </div>
                    <div class="td orders__btn" v-else-if="order.status_id == '02'">
                      <button class="btn btn--primary" @click="saveOrder(index,'03')">Собран<span v-if="order.deadline">{{ order.deadline }}</span></button>
                    </div>
                    <div class="td orders__btn" v-else>
                      <div class="orders__ready-date" v-if="order.status_id != '05'">Собран {{ localeDate(order.status_date) }}</div>
                      <button class="btn btn--gray" v-if="activeOrder !== order.id" @click="activeOrder = order.id">Подробнее</button>
                      <button class="btn btn--gray active" v-if="activeOrder === order.id" @click="activeOrder = null">Скрыть</button>
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
          orders2: false,
          htmlContent: '',
          page_no: 1,
          rows_per_page: 10,
          orders: {},
          pages: 1,
          filters: {},
          imgPath: productImgPathModerated,
          selectedFilters: {
            status_id: '',
            store_id: ''
          },
          filtersCreated: false,
          activeOrder: null,
          activeItem: null,
          currentQuantity: null,
          deadline_new: 10,
          deadline_new_last: 5,
          deadline_collect: 20,
          deadline_collect_last: 15,
        }
    },
    methods: {
      localedTime(ms){
        let minutes = Math.floor(ms / 60000),
            seconds = ((ms % 60000) / 1000).toFixed(0);
        return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
      },
      calulateTime(date,first,last){
        let current = new Date();
        let deadline = date + (first * 60 * 1000);
        if (+current > deadline){
          let new_deadline = date + ((first + last) * 60 * 1000);
          if (+current > new_deadline){
            return false;
          } else {
            //Вывод дополнительных минут
            return '-' + this.localedTime(current - deadline);
          }
        } else {
          //Вывод первых минут
          return this.localedTime(deadline - current);
        }
      },
      checkTime(){
        if (this.$route.name != 'orders'){
          clearInterval(this.timer);
        }
        for (order in this.orders){
          if (+order.status_id == '01'){
            order.deadline = this.calulateTime(order.date,this.deadline_new,this.deadline_new_last);
            console.log(order.date,this.deadline_new,this.deadline_new_last,order.deadline);
          } else if (+order.status_id == '02'){
            order.deadline = this.calulateTime(order.date,this.deadline_collect,this.deadline_collect_last);
            console.log(order.date,this.deadline_new,this.deadline_new_last,order.deadline);
            if (!order.deadline){
              //Сделать дополнительный запрос?
              order.status_id = '05';
              order.status = 'Отменен';
            }
          }
        }
      },
      setTime(){
        this.timer = setInterval(() => {
          this.checkTime();
        }, 1000);
      },
      countProducts(index) {
        return this.orders[index].items.filter(item => item.qty_partner > 0).length;
      },
      calculatePrice(price,discount) {
        return price - (price * discount / 100)
      },
      getUnit(value) {
        if (value == 0) {
          return 'товаров';
        }
        value = value.toString();
        if (value.slice(-1) == '1' && value.slice(-2) != '11') {
          return 'товар';
        }
        if (+value.slice(-1) > 1 && +value.slice(-1) < 5 && +value.slice(-2) != '12' && value.slice(-2) != '13' && value.slice(-2) != '14') {
          return 'товара';
        }
        return 'товаров';
      },
      localeDate(ms) {
        let dateObject = new Date(+ms);
        return dateObject.toLocaleString('ru-RU', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'});
      },
      saveOrder(index,status) {
        this.orders[index].status_id = status;
        let statuses = this.filters.statuses;
        for (item of statuses){
          if (item[0]==status) {
            this.orders[index].status = item[1];
          }
        }

        let orderRequestData = JSON.parse(JSON.stringify(this.orders[index]).replace('id', 'id'));
        console.log(orderRequestData);

        let requestUrl = '/control-panel/update-requisition';
        const headers = { 'X-Requested-With': 'XMLHttpRequest' }
        axios
          .post(requestUrl,
            Qs.stringify(orderRequestData), {headers})
            .then(response => {
              if (response.data.data === true) {
                location.reload();
              } else {
                console.log('Response from update-requisition',response.data);
              }
            })
            .catch(error => {
              console.log(error.response)
              if (error.response.status == '403'){
                location.reload();
              }
              $('.main__loader').hide();
            });

      },
      getOrders() {
        let requestUrl = '/control-panel/show-requisitions';
        if (this.filtersCreated) {
         requestUrl = '/control-panel/show-requisitions-from-cache';
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
                console.log('Response from show-requisitions',response.data);
                this.activeOrder = null;
                this.pages = response.data.data.limits.total;
                this.orders = response.data.data.body;
                if (!this.filtersCreated){
                  this.filters = response.data.data.filters;
                  this.filtersCreated = true;
                }
                this.setTime();
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
        this.getOrders();
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
