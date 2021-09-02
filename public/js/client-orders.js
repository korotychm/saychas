function getClientOrders(){
    $.ajax({
            beforeSend: function () {},
            url: "/ajax-get-order-list",
            type: 'POST',
            cache: false,
            success: function (data) {
                //if(data.result == 1)
                $("#order-list").html(JSON.stringify(data,  null, 2 ));
            },
            error: function (xhr, ajaxOptions, thrownError) {
              showServicePopupWindow(
                            "Ошибка " + xhr.status,
                            "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError
                            );
                return false;
            }
        });
}

$(document).ready(function () {
    getClientOrders();
});

$(document).ready(function () {

  var clientOrders = new Vue({
    el: '#client-orders',
    data: {
      orders: [],
      products: []
    },
    computed: {
      preparedOrders() {
        //if (!this.orders.deliveryInfo && this.orders.deliveryInfo.deliveries != undefined && this.orders.deliveryInfo.pickup != undefined) return this.orders;
        let orders = this.orders;
        for (order of orders){
          order.deliveryProducts = [];
          order.pickupProducts = [];
          order.total = 0;
          order.oldtotal = 0;
          order.count = 0;
          let orderDate = new Date(order.date * 1000);
          order.dateLocaled = orderDate.toLocaleString();

          let pickupCount = order.deliveryInfo.pickup.length;
          if (pickupCount == 1){
            order.pickupUnit = 'магазина';
          } else {
            order.pickupUnit = 'магазинов';
          }
          let deliveryCount = order.deliveryInfo.deliveries.length;
          if (deliveryCount == 1){
            order.deliveryUnit = 'доставка';
          } else if (deliveryCount > 1 && deliveryCount < 5) {
            order.deliveryUnit = 'доставки';
          } else {
            order.deliveryUnit = 'доставок';
          }

          for (delivery of order.deliveryInfo.deliveries){
            if (delivery.requisitions){
              for (requisition of delivery.requisitions){
                for (product of requisition.products){
                  product.img = this.products[product.id].image;
                  product.title = this.products[product.id].title;
                  order.deliveryProducts.push(product);
                  order.total += (product.price / 100);
                  if (+product.discount > 0) {
                    order.oldtotal += (product.price / (100 - product.discount));
                  } else {
                    order.oldtotal += (product.price / 100);
                  }
                  order.count++;
                }
              }
            }
          }
          for (delivery of order.deliveryInfo.pickup){
            if (delivery.requisitions){
              for (requisition of delivery.requisitions){
                for (product of requisition.products){
                  product.img = this.products[product.id].image;
                  product.title = this.products[product.id].title;
                  order.pickupProducts.push(product);
                  order.total += (product.price / 100);
                  if (+product.discount > 0) {
                    order.oldtotal += (product.price / (100 - product.discount));
                  } else {
                    order.oldtotal += (product.price / 100);
                  }
                  order.count++;
                }
              }
            }
          }
          order.productsTotal = order.total;
          order.oldProductsTotal = order.oldtotal;
          if (order.basketInfo.delivery_price){
            order.total += (order.basketInfo.delivery_price / 100);
            order.oldtotal += (order.basketInfo.delivery_price / 100);
          }
        }
        console.log(orders);
        return orders;
      }
    },
    methods: {
      getClientOrders() {
        axios
          .post('/ajax-get-order-list')
          .then(response => (
            this.orders = response.data.order_list,
            this.products = response.data.productsMap,
            console.log(this.orders)
          ));
      }
    },
    mounted() {
      this.getClientOrders()
    }
  });

});
