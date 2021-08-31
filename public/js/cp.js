
const Analytics = { template: '<div>Аналитика</div>' }
const Products = {
  data: function () {
    return {
      heading: 'Мои товары',
      page: 1
    }
  },
  template: '<div>Товары</div>',
  created: function(){
    axios
      .post('/control-panel/show-products',
        Qs.stringify({
          page_no : this.page
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
  mounted: function(){
    router.replace('/analytics');
  }
}).$mount('#cp')
