const ProductAddApi = {
  template: `<div>
                <div class="filter">
                  <div class="filter__btn">
                      <router-link to="/product-add" class="btn btn--secondary">По одному товару</router-link>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-file" class="btn btn--secondary">Массой товаров</router-link>
                  </div>
                  <div class="filter__btn">
                      <a class="btn btn--primary">Загрузка по API</a>
                  </div>
                </div>
                <div class="cp-container product">
                  <div class="product-add-api">
                      <div class="product-add-api__left">
                        <div class="product-add-api__key">
                          <p>Ключ для работы с API статистики x32:</p>
                          <h2>3caf1fea-748c-4258-90b1-c67897b206b</h2>
                        </div>
                        <div class="product-add-api__key">
                          <p>Ключ для работы с API статистики x64:</p>
                          <h2>M2NhZjFmZWEtNzQ4Yy00MjULTkwYjEtYzY3ODliN2IyMDZi</h2>
                        </div>
                      </div>
                      <div class="product-add-api__right">
                        <p><a href="#">Подробное описание АPI</a></p>
                        <p>При перевыпуске действующие ключи для работы с API статистики перестают работать, и вы получаете новые ключи.</p>
                        <button class="btn btn--primary">Перевыпустить ключ</button>
                        <p><i>Для удаления ключей обратитесь к своему менеджеру</i></p>
                      </div>
                  </div>
                </div>
            </div>`,
  mounted: function(){
    $('.main__loader').hide();
  }
}
