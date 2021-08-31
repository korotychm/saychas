
const Foo = { template: '<div>foo</div>' }

const routes = [
  { path: '/foo', component: Foo }
]

const router = new VueRouter({
  routes,
  mode: 'history'
})

const app = new Vue({
  router
}).$mount('#cp')
