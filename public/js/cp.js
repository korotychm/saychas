
const Analytics = { template: '<header class="header"><h1 class="header__heading"><span>Аналитика</span></h1><div class="header__user"><div class="header__user-name"><h2>Сергей Заказчиков</h2><p>Администратор</p></div><div class="header__user-avatar">С</div></div></header>' }
const Products = {
  data: function () {
    return {
      heading: 'Мои товары'
    }
  },
  template: '<header class="header"><h1 class="header__heading"><span>{{heading}}</span></h1><div class="header__user"><div class="header__user-name"><h2>Сергей Заказчиков</h2><p>Администратор</p></div><div class="header__user-avatar">С</div></div></header>',
  created: function(){
    axios
      .post('/ajax-get-category-filters',
        Qs.stringify({
          categoryId : '000000006'
        })).then(response => (
          console.log(response.data);
        ));
  }
}

const routes = [
  {
    name: 'analytics',
    path: '/control-panel/analytics',
    component: Analytics
  },
  {
    name: 'products',
    path: '/control-panel/products',
    component: Products
  }
]

const router = new VueRouter({
  routes,
  mode: 'history'
})

const cp = new Vue({
  router,
  mounted: function(){
    router.replace('/control-panel/analytics');
  }
}).$mount('#cp')
