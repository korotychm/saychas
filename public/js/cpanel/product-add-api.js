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
                      <a class="btn btn--secondary">Загрузка по API</a>
                  </div>
                </div>
                <div class="cp-container product"></div>
            </div>`,
  mounted: function(){
    $('.main__loader').hide();
  }
}
