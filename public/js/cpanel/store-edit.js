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
