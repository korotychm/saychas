
const Analytics = { template: '<div>Аналитика</div>' }
const ProductsFilters = {
  template: '<div><form class="filter"><div class="filter__search"><input class="input input--white" type="text" placeholder="Быстрый поиск" /><button><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" /></svg></button></div><div class="filter__select"><select class="select select--white"><option val="" selected="selected">Все категории</option><option val="">Смартфоны</option></select></div><div class="filter__select"><select class="select select--white"><option val="" selected="selected">Все бренды</option><option val="">Huawei</option></select></div><div class="filter__btn"><a class="btn btn--primary" href="/product-add.html">+ Добавить товары</a></div></form></div>',
  mounted: function(){
    $('.filter .select').niceSelect();
  }
}
Vue.component("ProductsFilters", ProductsFilters);
const Products = {
  data: function () {
    return {
      page_no: 1,
      rows_per_page: 2,
      products: {},
      pages: 1,
      imgPath: productImgPath
    }
  },
  template:
    `<div>
      <ProductsFilters></ProductsFilters>
      <div class="products__list">
        <div class="thead">
          <div class="td"></div>
          <div class="td">Наименование</div>
          <div class="td">Категория</div>
          <div class="td">Бренд</div>
        </div>
        <div class="tbody">
            <div v-for="product in products" class="tr">
                <div class="td products__img">
                  <img :src="(product.images.length) ? (imgPath + product.images[0]) : '/images/products/nophoto.jpg'" />
                </div>
                <div class="td products__title"><a href="/edit-product.html">{{ product.title }}</a></div>
                <div class="td products__category">
                    <div>{{ product.category_name }}</div>
                </div>
                <div class="td">{{ product.brand_name }}</div>
            </div>
        </div>
      </div>
      <div class="pagination">
        <a v-for="index in pages" :class="{active : (index == page_no)}" @click="loadPage(index)">{{ index }}</a>
      </div>
    </div>`,
  methods: {
    getProducts() {
      axios
        .post('/control-panel/show-products',
          Qs.stringify({
            page_no : this.page_no,
            rows_per_page : this.rows_per_page
          }))
          .then(response => (
            console.log(response.data.data.body),
            this.pages = response.data.data.limits.total,
            this.products = response.data.data.body
          ));
    },
    loadPage(index) {
      this.page_no = index;
      this.getProducts();
    }
  },
  created: function(){
    this.getProducts();
  }
}

const routes = [
  {
    name: 'analytics',
    path: '/analytics',
    meta: {
      h1: 'Аналитика'
    },
    component: Analytics
  },
  {
    name: 'products',
    path: '/products',
    meta: {
      h1: 'Мои товары',
      back_route: '/analytics'
    },
    component: Products
  }
]

const router = new VueRouter({
  routes,
  //mode: 'history'
})

const cp = new Vue({
  router,
  mounted: function(){
    router.replace('/analytics');
  }
}).$mount('#cp')
