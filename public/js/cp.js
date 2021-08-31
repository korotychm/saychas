
const Foo = { template: '<div>foo</div>' }

const routes = [
  { path: '/control-panel/foo', component: Foo }
]

const router = new VueRouter({
  routes,
  mode: 'history'
})

const app = new Vue({
  router,
  mounted: function(){
    router.replace('/control-panel/foo');
  }
}).$mount('#cp')
