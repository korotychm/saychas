
const Analytics = { template: '<div>Аналитика</div>' }
const ProductsFilters = {
  template: '<div><form class="filter"><div class="filter__search"><input class="input input--white" type="text" placeholder="Быстрый поиск" /><button><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26px" height="27px"><path fill-rule="evenodd" fill="rgb(255, 75, 45)" d="M24.696,26.998 C24.362,26.998 24.34,26.865 23.797,26.632 L18.232,21.156 L17.955,21.327 C16.78,22.481 13.927,23.91 11.736,23.91 C5.264,23.91 0.0,17.911 0.0,11.545 C0.0,5.177 5.264,0.1 11.736,0.1 C18.208,0.1 23.473,5.177 23.473,11.545 C23.473,14.370 22.408,17.101 20.475,19.233 L20.217,19.519 L25.623,24.836 C25.869,25.78 26.2,25.397 25.999,25.734 C25.995,26.70 25.853,26.386 25.600,26.625 C25.357,26.865 25.30,26.998 24.696,26.998 ZM11.736,2.527 C6.682,2.527 2.570,6.572 2.570,11.545 C2.570,16.517 6.682,20.562 11.736,20.562 C16.774,20.562 20.874,16.517 20.874,11.545 C20.874,6.572 16.774,2.527 11.736,2.527 Z" /></svg></button></div><div class="filter__select"><select class="select select--white"><option val="" selected="selected">Все категории</option><option val="">Смартфоны</option></select></div><div class="filter__select"><select class="select select--white"><option val="" selected="selected">Все бренды</option><option val="">Huawei</option></select></div><div class="filter__btn"><a class="btn btn--primary" href="/product-add.html">+ Добавить товары</a></div></form></div>'
}
const Products = {
  data: function () {
    return {
      heading: 'Мои товары',
      page_no: 1,
      rows_per_page: 2
    }
  },
  template: '<div><ProductsFilters></ProductsFilters></div>',
  created: function(){
    axios
      .post('/control-panel/show-products',
        Qs.stringify({
          page_no : this.page_no,
          rows_per_page : this.rows_per_page
        }))
        .then(response => (
          console.log(response.data)
        ));
  }
}

const routes = [
  {
    name: 'analytics',
    path: '/analytics',
    component: Analytics,
    title: 'Аналитика'
  },
  {
    name: 'products',
    path: '/products',
    component: Products,
    title: 'Мои товары'
  }
]

const router = new VueRouter({
  routes,
  //mode: 'history'
})

const cp = new Vue({
  router,
  computed: {
    getH1() {
      return this.$route.title;
    }
  },
  mounted: function(){
    router.replace('/analytics');
  }
}).$mount('#cp')
