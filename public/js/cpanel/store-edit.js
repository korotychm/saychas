const StoreEdit = {
  template: `<div class="cp-container store">
                <div v-if="editable">
                  <div v-if="store.id">
                    <div class="store__fields">
                      <div class="product__attribute product__attribute--short">
                        <h2 :class="{'input-error' : (!store.title && errors)}">Название магазина <span class="required">*</span></h2>
                        <input type="text" class="input" v-model="store.title" />
                      </div>
                      <div class="product__attribute">
                        <h2 :class="{'input-error' : (!store.address && errors)}">Адрес <span class="required">*</span></h2>
                        <div>
                          <input type="text" class="input suggestions-input" v-model="store.address" id="store-address" placeholder="Начните вводить адрес..." pattern="[A-Za-zА-Яа-яЁё]{3,}" accept="" />
                          <input type="hidden" class="input" v-model="store.geox" id="geox" />
                          <input type="hidden" class="input" v-model="store.geoy" id="geoy" />
                          <input type="hidden" class="input" v-model="store.dadata" id="dadata" />
                          <p class="error" id="store-address-error"></p>
                        </div>
                      </div>
                      <div class="product__attribute">
                        <h2>Комментарий</h2>
                        <div>
                          <input type="text" class="input" v-model="store.description" />
                          <p>Комментарий для курьера — что бы легче находить и быстрее приезжать</p>
                        </div>
                      </div>
                      <div class="product__attribute">
                        <h2 :class="{'input-error' : (!store.contact_name && errors)}">Контактное лицо <span class="required">*</span></h2>
                        <div>
                          <input type="text" v-model="store.contact_name" class="input" />
                        </div>
                      </div>
                      <div class="product__attribute product__attribute--short">
                        <h2 :class="{'input-error' : (!store.contact_phone && errors)}">Телефон <span class="required">*</span></h2>
                        <div>
                          <input v-model="store.contact_phone" v-mask="'+7 (###) ###-##-##'" class="input" type="text" placeholder="+7 (999) 999-99-99" />
                        </div>
                      </div>
                    </div>
                    <div class="store__fields">
                      <div class="product__attribute product__attribute--short">
                        <h2>Статус работы <span class="required">*</span></h2>
                        <div>
                          <div class="custom-select custom-select--radio">
                            <div class="custom-select__label input"></div>
                            <div class="custom-select__dropdown">
                              <label class="custom-select__option">
                                <input type="radio" :checked="store.status_id == 0" value="0" name="status" v-model="store.status_id" />
                                <span>Работает по графику</span>
                              </label>
                              <label class="custom-select__option">
                                <input type="radio" :checked="store.status_id == 1" value="1" name="status" v-model="store.status_id" />
                                <span>Временно не работает</span>
                              </label>
                              <label class="custom-select__option">
                                <input type="radio" :checked="store.status_id == 2" value="2" name="status" v-model="store.status_id" />
                                <span>Закрыт</span>
                              </label>
                            </div>
                          </div>
                          <p>Статус устанавливает глобальный режим работы магазина.</p>
                          <p>Для изменения работы в определенные дни - восользуйтесь полями ниже.</p>
                        </div>
                      </div>
                      <div class="store__timetable">
                        <div class="store__timetable-inputs">
                          <div class="store__timetable-main active">
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.working_day_from == '00:00' && store.operating_mode.working_day_to == '00:00')}">
                              <h2 :class="{'input-error' : ((!store.operating_mode.working_day_from || !store.operating_mode.working_day_to) && errors)}">Рабочие дни  <span class="required">*</span><span class="store__timetable-trigger" @click="dayOff('working_day')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.working_day_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.working_day_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.saturday_from == '00:00' && store.operating_mode.saturday_to == '00:00')}">
                              <h2 :class="{'input-error' : ((!store.operating_mode.saturday_from || !store.operating_mode.saturday_to) && errors)}">Суббота  <span class="required">*</span><span class="store__timetable-trigger" @click="dayOff('saturday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.saturday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.saturday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.sunday_from == '00:00' && store.operating_mode.sunday_to == '00:00')}">
                              <h2 :class="{'input-error' : ((!store.operating_mode.sunday_from || !store.operating_mode.sunday_to) && errors)}">Воскресенье  <span class="required">*</span><span class="store__timetable-trigger" @click="dayOff('sunday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.sunday_from"/>
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.sunday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.holiday_from == '00:00' && store.operating_mode.holiday_to == '00:00')}">
                              <h2 :class="{'input-error' : ((!store.operating_mode.holiday_from || !store.operating_mode.holiday_to) && errors)}">Праздничные дни  <span class="required">*</span><span class="store__timetable-trigger" @click="dayOff('holiday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.holiday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model="store.operating_mode.holiday_to" />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="store__calendar">
                          <v-date-picker v-model='selectedDate' />
                        </div>
                      </div>
                      <p>Если магазин работает круглосуточно - проставьте с 00:00 до 23:59</p>
                      <p>Если магазин не работает, например, по субботам - нажмите крестик напротив.</p>
                      <p>Для изменения работы в определенную дату - выберите её на календаре.</p>
                    </div>
                    <div class="product__save-back">
                      <router-link :to="$route.meta.back_route" class="btn btn--secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="8px" height="13px">
                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M0.903,4.974 L4.974,0.903 C5.560,0.317 6.510,0.317 7.96,0.903 C7.681,1.489 7.681,2.439 7.96,3.25 L3.25,7.96 C2.439,7.681 1.489,7.681 0.903,7.96 C0.318,6.510 0.318,5.560 0.903,4.974 Z" />
                            <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.96,9.974 L3.25,5.903 C2.439,5.317 1.489,5.317 0.903,5.903 C0.318,6.489 0.318,7.439 0.903,8.25 L4.974,12.96 C5.560,12.681 6.510,12.681 7.96,12.96 C7.681,11.510 7.681,10.560 7.96,9.974 Z" />
                        </svg>
                        <span>Вернуться</span>
                      </router-link>
                      <button class="btn btn--primary" @click="saveStore">Сохранить изменения</button>
                    </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот магазин</div>
              </div>
            `,
  data: function () {
    return {
      errors: false,
      editable: true,
      selectedDate: null,
      store: {
        operating_mode: {}
      }
    }
  },
  methods: {
    dayOff(day) {
      if (this.store.operating_mode[day + '_from'] == '00:00' && this.store.operating_mode[day + '_to'] == '00:00'){
        this.store.operating_mode[day + '_to'] = '23:59';
      } else {
        this.store.operating_mode[day + '_from'] = '00:00';
        this.store.operating_mode[day + '_to'] = '00:00';
      }
    },
    checkRequired(){
      if (!this.store.title || !this.store.address || !this.store.contact_name || !this.store.contact_phone){
        return true;
      }
      for (item in this.store.operating_mode){
        if (!store.operating_mode[item]){
          return true;
        }
      }
      return false;
    },
    saveStore(){
      this.errors = this.checkRequired();
      if (!this.errors){
        let request = JSON.parse(JSON.stringify(this.store));
        request.address = this.store.dadata;
        let requestUrl = '/control-panel/update-store';
        const headers = { 'X-Requested-With': 'XMLHttpRequest' };
        request.contact_phone = request.contact_phone.replace(/ /g,'').replace(/\+/g,'').replace(/\(/g,'').replace(/\)/g,'').replace(/-/g,'');
        delete request.dadata;
        console.log(request);
        axios
          .post(requestUrl,
            Qs.stringify({
              data : {
                store: request
              }
            }),{headers})
            .then(response => {
              if (response.data.result){
                router.replace('/stores');
              }
            })
            .catch(error => {
              if (error.response.status == '403'){
                this.editable = false;
                $('.main__loader').hide();
              }
            });
      }
    },
    storeDaData(){
      $("#store-address").suggestions({
          token: "af6d08975c483758059ab6f0bfff16e6fb92f595",
          type: "ADDRESS",
          onSelect: function (suggestion) {
              $("#store-address-error").hide();
              if (!suggestion.data.house)
              {
                  $("#store-address-error").html("Необходимо указать адрес до номера дома!").show();
                  return false;
              }
              var dataString = JSON.stringify(suggestion);
              $('#geox').val(suggestion.data.geo_lat)[0].dispatchEvent(new Event('input'));
              $('#geoy').val(suggestion.data.geo_lon)[0].dispatchEvent(new Event('input'));
              $('#dadata').val(dataString)[0].dispatchEvent(new Event('input'));
          }
      });
    },
    getStore() {
      let requestUrl = '/control-panel/edit-store';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
        .post(requestUrl,
          Qs.stringify({
            store_id : this.$route.params.id
          }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.store = response.data.store;
              this.store.dadata = '';
              console.log(this.store);
              setTimeout(() => {
                this.storeDaData();
              }, 200);
              $('.main__loader').hide();
            }
          })
          .catch(error => {
            if (error.response.status == '403'){
              this.editable = false;
              $('.main__loader').hide();
            }
          });
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getStore();
  },
  updated: function(){
    setAllCustomSelects();
  }
}
