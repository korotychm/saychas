const StoreEdit = {
  template: `<div class="cp-container store">
                <div v-if="editable">
                  <div v-if="store.id">
                    <div class="store__fields">
                      <div class="product__attribute product__attribute--short">
                        <h2>Название магазина</h2>
                        <input type="text" class="input" v-model="store.title" />
                      </div>
                      <div class="product__attribute">
                        <h2>Адрес</h2>
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
                    </div>
                    <div class="store__fields">
                      <div class="product__attribute product__attribute--short">
                        <h2>Статус работы</h2>
                        <div>
                          <select class="select" v-model="store.status_id">
                            <option value="0">Работает по графику</option>
                            <option value="1">Временно не работает</option>
                            <option value="2">Закрыт</option>
                          </select>
                          <p>Статус устанавливает глобальный режим работы магазина.</p>
                          <p>Для изменения работы в определенные дни - восользуйтесь полями ниже.</p>
                        </div>
                      </div>
                      <div class="store__timetable">
                        <div class="store__timetable-inputs">
                          <div class="store__timetable-main active">
                            <div class="store__timetable-item product__attribute">
                              <h2>Рабочие дни <span class="store__timetable-trigger"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.working_day_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.working_day_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute">
                              <h2>Суббота <span class="store__timetable-trigger"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.saturday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.saturday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute">
                              <h2>Воскресенье <span class="store__timetable-trigger"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.sunday_from"/>
                                </div>
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.sunday_to" />
                                </div>
                              </div>
                            </div>
                            <div class="store__timetable-item product__attribute">
                              <h2>Праздничные дни <span class="store__timetable-trigger"></span></h2>
                              <div class="input-group">
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.holiday_from" />
                                </div>
                                <div>
                                  <input type="text" class="timeinput" v-model="store.operating_mode.holiday_to" />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="store__calendar">
                          <v-date-picker v-model='selectedDate' />
                        </div>
                      </div>
                      <p>Если магазин работает круглосуточно - проставьте с 00:00 до 00:00</p>
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
      editable: true,
      selectedDate: null,
      store: {
        dadata: ''
      }
    }
  },
  methods: {
    saveStore(){
      console.log(this.store);
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
              $('#geox').val(suggestion.data.geo_lat);
              $('#geoy').val(suggestion.data.geo_lon);
              $('#dadata').val(dataString);
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
              console.log(this.store);
              setTimeout(() => {
                this.storeDaData()
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
  }
}

$(document).on('click','.store__timetable-trigger', function(){
  $(this).parent().parent().toggleClass('closed');
});
