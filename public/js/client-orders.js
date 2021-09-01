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
        let orders = this.orders;
        for (order of orders){
          order.deliveryProducts = [];
          order.pickupProducts = [];
          order.total = 0;
          order.oldtotal = 0;
          let orderDate = new Date(order.date * 1000);
          order.dateLocaled = orderDate.toLocaleString();

          for (delivery of order.deliveryInfo.deliveries){
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
              }
            }
          }
          for (delivery of order.deliveryInfo.pickup){
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
              }
            }
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
