const StoreEdit = {
  template: `<div class="cp-container store">
                <div v-if="editable">
                  <div class="store__fields">
                    <div class="product__attribute  product__attribute--short">
                      <h2>Название магазина</h2>
                      <input type="text" class="input" />
                    </div>
                    <div class="product__attribute">
                      <h2>Адрес</h2>
                      <input type="text" class="input" />
                    </div>
                    <div class="product__attribute">
                      <h2>Комментарий</h2>
                      <div>
                        <input type="text" class="input" />
                        <p>Комментарий для курьера — что бы легче находить и быстрее приезжать</p>
                      </div>
                    </div>
                  </div>
                  <div class="store__fields">
                    <div class="product__attribute product__attribute--short">
                      <h2>Статус работы</h2>
                      <div>
                        <select class="select" value="1">
                          <option value="1">Работает по графику</option>
                          <option value="2">Временно не работает</option>
                          <option value="2">Закрыт</option>
                        </select>
                        <p>Статус устанавливает глобальный режим работы магазина.<br> Для изменения работы в определенные дни - восользуйтесь полями ниже.</p>
                      </div>
                    </div>
                    <div class="store__timetable">
                      <div class="store__timetable-inputs">
                        <div class="store__timetable-main active">
                          <div class="product__attribute">
                            <h2>Рабочие дни</h2>
                            <div class="input-group">
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                            </div>
                          </div>
                          <div class="product__attribute">
                            <h2>Суббота</h2>
                            <div class="input-group">
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                            </div>
                          </div>
                          <div class="product__attribute">
                            <h2>Воскресенье</h2>
                            <div class="input-group">
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                            </div>
                          </div>
                          <div class="product__attribute">
                            <h2>Праздничные дни</h2>
                            <div class="input-group">
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                              <div>
                                <input type="text" class="timeinput" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="product__error">Вы не можете редактировать этот магазин</div>
              </div>
            `,
  data: function () {
    return {
      editable: true
    }
  },

}
