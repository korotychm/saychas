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
                <div class="cp-container product"></div>
            </div>`,
  mounted: function(){
    $('.main__loader').hide();
  }
}
