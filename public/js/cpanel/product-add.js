const ProductAdd = {
  template: `<div>
                <div class="filter">
                  <div class="filter__btn">
                      <a class="btn btn--primary">По одному товару</a>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-file" class="btn btn--secondary">Массой товаров</router-link>
                  </div>
                  <div class="filter__btn">
                      <router-link to="/product-add-api" class="btn btn--secondary">Загрузка по API</router-link>
                  </div>
                </div>
                <div class="cp-container product"></div>
            </div>`,
  mounted: function(){
    $('.main__loader').hide();
  }
}
