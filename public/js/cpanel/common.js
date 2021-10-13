const routes = [
  {
    name: 'orders',
    path: '/orders',
    meta: {
      h1: 'Заказы и возвраты'
    },
    component: Orders
  },
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
      h1: 'Мои товары'
    },
    component: Products
  },
  {
    name: 'stores',
    path: '/stores',
    meta: {
      h1: 'Магазины'
    },
    component: Stores
  },
  {
    name: 'stores-map',
    path: '/stores-map',
    meta: {
      h1: 'Магазины на карте'
    },
    component: StoresMap
  },
  {
    name: 'inventory',
    path: '/inventory',
    meta: {
      h1: 'Товарные остатки'
    },
    component: Inventory
  },
  {
    name: 'product-edit',
    path: '/products/:id',
    meta: {
      h1: 'Редактирование товара',
      back_route: '/products'
    },
    component: ProductEdit
  },
  {
    name: 'product-add',
    path: '/product-add',
    meta: {
      h1: 'Добавление товара',
      back_route: '/products'
    },
    component: ProductAdd
  },
  {
    name: 'product-add-file',
    path: '/product-add-file',
    meta: {
      h1: 'Добавление товаров',
      back_route: '/products'
    },
    component: ProductAddFile
  },
  {
    name: 'product-add-api',
    path: '/product-add-api',
    meta: {
      h1: 'Загрузка товаров по API',
      back_route: '/products'
    },
    component: ProductAddApi
  },
  {
    name: 'store-edit',
    path: '/stores/:id',
    meta: {
      h1: 'Редактирование магазина',
      back_route: '/stores'
    },
    component: StoreEdit
  },
  {
    name: 'store-add',
    path: '/store-add',
    meta: {
      h1: 'Добавление магазина',
      back_route: '/stores'
    },
    component: StoreAdd
  },
  {
    name: 'api',
    path: '/api-integration',
    meta: {
      h1: 'Интеграция API'
    },
    component: ApiIntegration
  },
  {
    name: 'price-list',
    path: '/price-list',
    meta: {
      h1: 'Цены и скидки'
    },
    component: PriceList
  },
  {
    name: 'testimonials',
    path: '/testimonials',
    meta: {
      h1: 'Отзывы'
    },
    component: Testimonials
  },
  {
    name: 'instructions',
    path: '/instructions',
    meta: {
      h1: 'Инструкции'
    },
    component: Instructions
  },
  {
    name: 'documents',
    path: '/documents',
    meta: {
      h1: 'Документы'
    },
    component: Documents
  },
  {
    name: 'support',
    path: '/support',
    meta: {
      h1: 'Поддержка'
    },
    component: Support
  },
  {
    name: 'settings',
    path: '/settings',
    meta: {
      h1: 'Управление аккаунтом'
    },
    component: Support
  }
]

const router = new VueRouter({
  routes,
  //mode: 'history'
})

const cp = new Vue({
  router,
  mounted: function(){
    if(!this.$route.name) router.replace('/stores');
  }
}).$mount('#cp')
