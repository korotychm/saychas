const routes = [
  {
    name: 'orders',
    path: '/orders',
    meta: {
      h1: 'Поток заказов'
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
    if(!this.$route.name) router.replace('/products');
  }
}).$mount('#cp')

function showMessage(message) {
  $('.cp-message').html(message);
  $('.cp-message').addClass('active');
  setTimeout(function(){
    $('.cp-message').removeClass('active');
  }, 2500);
}


function useKeyboardEvents () {
  // let customDropdown = $('.custom-select__dropdown')
  // console.log($('.active').children('.custom-select__dropdown').children().children())

  // if (e.target === document.querySelector('.custom-select__label')) {
  //   let customSelectDropdown = $(e.target).next()
  // let $items = customSelectDropdown.children().children().children('span')
        let $items =  $('.active').children('.custom-select__dropdown').children().children().children('span')
        let $selected = $items.filter('.selected').removeClass('selected')
        let $next;
  $(document).on( "keydown", function(e) {
     if ($('.custom-select--radio').hasClass('active')) {
        if (e.keyCode === 40) {
          e.preventDefault()
          if (!$selected.length) {
            $next = $items.parent().first().children('span');
          } else {
            $next = $selected.is($items.parent().last().children('span')) ? $items.parent().first().children('span') : $selected.parent().next().children('span');
          }
          $next.addClass('selected')
        }
        if (e.keyCode === 	38) {
          e.preventDefault()
          if (!$selected.length) {
            $next = $items.parent().last().children('span');
          } else {
            $next = $selected.is($items.parent().first().children('span')) ? $items.parent().last().children('span') : $selected.parent().prev().children('span');
          }
          $next.addClass('selected')
        }
        if (e.keyCode === 13) {
          e.preventDefault()
          console.log($next)
          console.log($next.siblings('input[type="radio"]'))
          $next.siblings('input[type="radio"]').checked = true;
        }
      }
  })


}