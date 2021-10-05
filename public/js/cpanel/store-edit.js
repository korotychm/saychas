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
                          <input v-model.lazy="store.contact_phone" v-mask="'+7 (###) ###-##-##'" class="input" type="text" placeholder="+7 (999) 999-99-99" />
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
                              <div class="custom-select__dropdown-inner">
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
                          </div>
                          <p>Статус устанавливает глобальный режим работы магазина.<br>
                          Для изменения работы в определенные дни воспользуйтесь полями ниже</p>
                        </div>
                      </div>
                      <div class="store__timetable">

                        <div class="store__timetable-inputs">
                          <div v-if="selectedDate" class="store__timetable-additional">

                            <div class="store__timetable-item product__attribute"  :class="{closed : (modified_date.time_from == '00:00' && modified_date.time_to == '00:00')}">
                              <h2>{{ humanDate }} <span class="store__timetable-trigger" @click="dayOff('mod')"></span></h2>
                                <div class="input-group">
                                  <div>
                                    <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="modified_date.time_from" />
                                  </div>
                                  <div>
                                    <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="modified_date.time_to" />
                                  </div>
                                </div>
                            </div>
                            <div class="store__timetable-additional-btns">
                              <div class="btn btn--secondary" @click="selectedDate = null">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="8px" height="13px">
                                    <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M0.903,4.974 L4.974,0.903 C5.560,0.317 6.510,0.317 7.96,0.903 C7.681,1.489 7.681,2.439 7.96,3.25 L3.25,7.96 C2.439,7.681 1.489,7.681 0.903,7.96 C0.318,6.510 0.318,5.560 0.903,4.974 Z" />
                                    <path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M7.96,9.974 L3.25,5.903 C2.439,5.317 1.489,5.317 0.903,5.903 C0.318,6.489 0.318,7.439 0.903,8.25 L4.974,12.96 C5.560,12.681 6.510,12.681 7.96,12.96 C7.681,11.510 7.681,10.560 7.96,9.974 Z" />
                                </svg>
                                <span>Вернуться</span>
                              </div>
                              <div class="btn btn--secondary" @click="delDate">Сбросить</div>
                              <div class="btn btn--primary" @click="saveDate">Сохранить</div>
                            </div>
                            <p v-if="(modified_date.time_from == '' || modified_date.time_to == '') && modified_date.error" class="input-error">Время работы должно быть заполнено</p>
                          </div>

                          <div v-else class="store__timetable-main">
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.working_day_from == '00:00' && store.operating_mode.working_day_to == '00:00')}">
                              <h2><span :class="{'input-error' : ((!store.operating_mode.working_day_from || !store.operating_mode.working_day_to) && errors)}">Рабочие дни <span class="required">*</span></span><span class="store__timetable-trigger" @click="dayOff('working_day')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.working_day_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.working_day_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.saturday_from == '00:00' && store.operating_mode.saturday_to == '00:00')}">
                              <h2><span :class="{'input-error' : ((!store.operating_mode.saturday_from || !store.operating_mode.saturday_to) && errors)}">Суббота <span class="required">*</span></span><span class="store__timetable-trigger" @click="dayOff('saturday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.saturday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.saturday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.sunday_from == '00:00' && store.operating_mode.sunday_to == '00:00')}">
                              <h2><span :class="{'input-error' : ((!store.operating_mode.sunday_from || !store.operating_mode.sunday_to) && errors)}">Воскресенье <span class="required">*</span></span><span class="store__timetable-trigger" @click="dayOff('sunday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.sunday_from"/>
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.sunday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute" :class="{closed : (store.operating_mode.holiday_from == '00:00' && store.operating_mode.holiday_to == '00:00')}">
                              <h2><span :class="{'input-error' : ((!store.operating_mode.holiday_from || !store.operating_mode.holiday_to) && errors)}">Праздничные дни <span class="required">*</span></span><span class="store__timetable-trigger" @click="dayOff('holiday')"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.holiday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" placeholder="00:00" v-mask="'##:##'" v-model.lazy="store.operating_mode.holiday_to" />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="store__calendar">
                          <v-date-picker v-model='selectedDate' :min-date='new Date()' @update:from-page="modifiedDaysHighlight" @update:to-page="modifiedDaysHighlight" />
                        </div>
                        <div v-html="highlightedStyles"></div>
                      </div>
                      <p>Если магазин работает круглосуточно - проставьте с 00:00 до 23:59<br>
                        Если магазин не работает, например, по субботам - нажмите крестик напротив.<br>
                        Для изменения работы в определенную дату - выберите её на календаре.</p>
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
      highlightedStyles: '',
      modified_date: {
        date: '',
        time_from: '',
        time_to: '',
        error: false
      },
      store: {
        operating_mode: {}
      }
    }
  },
  watch: {
    store: {
      deep: true,
      handler() {
        for (item in this.store.operating_mode){
          this.checkTime(item);
        }
        this.checkPhone();
      }
    },
    modified_date: {
      deep: true,
      handler() {
        this.checkTime();
      }
    },
    selectedDate() {
      if (this.selectedDate){
        console.log(this.store.modified_mode);
        this.modified_date.error = false;
        let index = this.checkModifiedDate();
        if (index != -1){
          this.modified_date.time_from = this.store.modified_mode[index].time_from;
          this.modified_date.time_to = this.store.modified_mode[index].time_to;
        } else {
          this.modified_date.time_from = '';
          this.modified_date.time_to = '';
        }
      }
    }
  },
  computed: {
    humanDate(){
      if (this.selectedDate){
        return this.selectedDate.toLocaleString("ru-RU",{
          day: 'numeric',
          month: 'long',
          year: 'numeric'
        });
      }
      return '';
    }
  },
  methods: {
    checkModifiedDate() {
      let localedDate = this.selectedDate.toLocaleString("ru-RU",{
        day: 'numeric',
        month: 'numeric',
        year: 'numeric'
      });
      this.modified_date.date = localedDate;
      return (this.store.modified_mode.findIndex(x => x.date === localedDate));
    },
    delDate(){
      let index = this.checkModifiedDate();
      if (index != -1){
        this.store.modified_mode.splice(index, 1);
      }
      console.log(this.store.modified_mode);
      this.selectedDate = null;
      this.modifiedDaysHighlight();
    },
    saveDate(){
      this.modified_date.error = false;
      if (this.modified_date.time_from == '' || this.modified_date.time_to == ''){
        this.modified_date.error = true;
      }
      if (!this.modified_date.error){
        let index = this.checkModifiedDate();
        if (index != -1){
          this.store.modified_mode[index] = JSON.parse(JSON.stringify(this.modified_date));
        } else {
          let newDate = JSON.parse(JSON.stringify(this.modified_date));
          this.store.modified_mode.push(newDate);
        }
        console.log(this.store.modified_mode);
        this.selectedDate = null;
        this.modifiedDaysHighlight();
      }
    },
    modifiedDaysHighlight() {
      let highlighted = '<style>';
      for (item in this.store.modified_mode){
        if (highlighted != '<style>'){
          highlighted += ', ';
        }
        let date = this.store.modified_mode[item].date,
            dateDay = date.split('.')[0],
            dateMonth = date.split('.')[1],
            dateYear = date.split('.')[2],
            className = '.id-' + dateYear + '-' + dateMonth + '-' + dateDay;
        highlighted += className + ' .vc-day-content';
      }
      highlighted += `{
        background: var(--red) !important;
        font-weight: bold !important;
        color: #fff !important;
      }`;
      highlighted += '</style>';
      this.highlightedStyles = highlighted;
    },
    checkTime(item = false){
      let regex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
      if (item && !regex.test(this.store.operating_mode[item])){
        this.store.operating_mode[item] = '';
      }
      if (!item  && !regex.test(this.modified_date.time_from)) {
        this.modified_date.time_from = '';
      }
      if (!item  && !regex.test(this.modified_date.time_to)) {
        this.modified_date.time_to = '';
      }
    },
    checkPhone(){
      let phone = this.store.contact_phone.replace(/ /g,'').replace(/\+/g,'').replace(/\(/g,'').replace(/\)/g,'').replace(/-/g,'');
      if (phone.length != 11){
        this.store.contact_phone = '';
      }
    },
    dayOff(day) {
      if (day == 'mod'){
        if (this.modified_date.time_from == '00:00' && this.modified_date.time_to == '00:00'){
          this.modified_date.time_to = '23:59';
        } else {
          this.modified_date.time_from = '00:00';
          this.modified_date.time_to = '00:00';
        }
      } else {
        if (this.store.operating_mode[day + '_from'] == '00:00' && this.store.operating_mode[day + '_to'] == '00:00'){
          this.store.operating_mode[day + '_to'] = '23:59';
        } else {
          this.store.operating_mode[day + '_from'] = '00:00';
          this.store.operating_mode[day + '_to'] = '00:00';
        }
      }
    },
    checkRequired(){
      if (!this.store.title || !this.store.address || !this.store.contact_name || !this.store.contact_phone){
        return true;
      }
      for (item in this.store.operating_mode){
        if (!this.store.operating_mode[item]){
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
      } else {
        showServicePopupWindow('Невозможно сохранить магазин', 'Пожалуйста, заполните все необходимые поля (отмечены <span class="required">*</span>)');
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
                  $("#store-address-error").html("Укажите адрес до номера дома!").show();
                  return false;
              }
              console.log(suggestion.data);
              var dataString = JSON.stringify(suggestion);
              console.log(dataString);
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
                this.modifiedDaysHighlight();
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
