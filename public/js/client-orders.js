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
        for (order in orders){
          order.products = [];
          for (delivery in order.deliveryInfo.deliveries){
            for (requisition in delivery.requisitions){
              for (product in requisition.products){
                order.products.push(product);
              }
            }
          }
        }
        console.log('computed',orders);
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
