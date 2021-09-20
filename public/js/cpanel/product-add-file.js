const ProductAddFile = {
  template: `<div>
                <div class="filter">
                  <div class="filter__btn">
                      <router-link to="/product-add" class="btn btn--secondary">По одному товару</router-link>
                  </div>
                  <div class="filter__btn">
                      <a class="btn btn--primary">Массой товаров</a>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-api" class="btn btn--secondary">Загрузка по API</router-link>
                  </div>
                </div>
                <div class="cp-container product product-add-file">
                  <div class="product__category">
                      <h2>Категория</h2>
                      <div class="search-select">
                          <input class="input search-select__input" type="text" value="" />
                      </div>
                  </div>
                  <div class="product-add-file__files">
                    <div class="product-add-file__files-download">
                      <a href="#">Скачать файл</a>
                      <p>Скачайте и заполните файл.</p>
                      <p>Чем больше полей заполните - тем легче пользователям будет найти ваш товар.</p>
                    </div>
                    <div class="product-add-file__files-upload">
                      <input type="file" id="upload-file"/>
                      <label for="upload-file">Загрузить файл</label>
                      <p>Загрузите заполненный файл.</p>
                      <p>Товары появятся на сайте после обработки.</p>
                    </div>
                  </div>
                </div>
            </div>`,
  mounted: function(){
    $('.main__loader').hide();
  }
}
