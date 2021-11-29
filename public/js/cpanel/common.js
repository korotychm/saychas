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

  function filterFunction() {
    var input, filter, div, span, i;
    input = document.querySelector(".custom-select__label");
    filter = input.value.toUpperCase();
    div = document.querySelector(".custom-select__dropdown-inner");
    span = div.getElementsByTagName("span");
    for (i = 0; i < span.length; i++) {
      let  txtValue = span[i].textContent || span[i].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        span[i].style.display = "";
      } else {
        span[i].style.display = "none";
        console.log(span[i])
        console.log(txtValue)
      }
    }
  }

    $(document).on('click','.custom-select--radio', function () {
      let input = document.createElement('input')
      input.placeholder = this.selectedValue;
      input.classList.add('custom-select__label')
      input.classList.add('input')
      let div = document.querySelector(".custom-select__label")
      div.replaceWith(input)
      input.focus()
      input.oninput = function () {
        let input, filter, div, span, i;
        filter = input.value.toUpperCase();
        div = document.querySelector(".custom-select__dropdown-inner");
        span = div.getElementsByTagName("span");
        for (i = 0; i < span.length; i++) {
          let  txtValue = span[i].textContent || span[i].innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            span[i].style.display = "";
          } else {
            span[i].style.display = "none";
          }
        }
      }
    })

    // let customSelectDropDownY = 0;

  document.addEventListener('keydown', function(e) {

    if (e.keyCode === 9) {
      $('.boolean').attr('tabindex', 0);
      $('.custom-select__label').attr('tabindex', 0)
      $('.additional-attributes').attr('tabindex', 0)
      $('label[for="additional-attributes"]').attr('tabindex', 0);
      let currentDrop = $('.custom-select__label')
      if (currentDrop.parent().hasClass('active')) {
        currentDrop.parent().removeClass('active')
      }
    }
    if (e.keyCode === 13) {
      if(document.activeElement.attributes.for && document.activeElement.attributes.for.textContent === 'additional-attributes') {
        $(document.activeElement).trigger('click')
      }
      if(document.activeElement.className === 'boolean') {
        $(document.activeElement).children('input').trigger('click')
      }
      if ($('.custom-select--radio').hasClass('active') || $('.custom-select--checkboxes').hasClass('active')) {
        e.preventDefault()
        $('.selected').siblings().trigger('click')
        $('.custom-select--radio').removeClass('active')
      } else {
        document.activeElement.parentNode.classList.add('active')
      }
    }
    let $items =  $('.active').children('.custom-select__dropdown').children().children().children('span')
    let $selected = $items.filter('.selected').removeClass('selected')
    let $next;

     if ($('.custom-select--radio').hasClass('active') || $('.custom-select--checkboxes').hasClass('active')) {
        if (e.keyCode === 40) {
          e.preventDefault()
          if (!$selected.length) {
            $next = $items.parent().first().children('span');
          } else {
            $next = $selected.is($items.parent().last().children('span')) ? $items.parent().first().children('span') : $selected.parent().next().children('span');
          }
          let position = $next.position()
          let y = position.top
          $('.custom-select__dropdown-inner').scrollTop(y + 17)
          $next.addClass('selected')
          // customSelectDropDownY += 17.25
        }
        if (e.keyCode === 	38) {
          e.preventDefault()
          if (!$selected.length) {
            $next = $items.parent().last().children('span');
          } else {
            $next = $selected.is($items.parent().first().children('span')) ? $items.parent().last().children('span') : $selected.parent().prev().children('span');
          }
          let position = $next.position()
          let y = position.top
          $('.custom-select__dropdown-inner').scrollTop(y + 17)
          $next.addClass('selected')
          // customSelectDropDownY -= 17.25
        }
      }
  })

}

useKeyboardEvents()