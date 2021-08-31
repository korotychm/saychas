
const Foo = { template: '<div>foo</div>' }

const routes = [
  {
    name: 'analytics',
    path: '/control-panel/analytics',
    component: Foo }
]

const router = new VueRouter({
  routes,
  mode: 'history'
})

const app = new Vue({
  router,
  mounted: function(){
    router.replace('/control-panel/analytics');
  }
}).$mount('#cp')
