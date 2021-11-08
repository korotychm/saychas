// function getClientOrder(){
//     var orderId = window.location.href.split("/").slice(-1)[0];
//     $.ajax({
//             beforeSend: function () {},
//             url: "/ajax-get-order-page",
//             type: 'POST',
//             cache: false,
//             data:{"orderId":orderId},
//             success: function (data) {
//                 //if(data.result == 1)
//                 $("#client-order").html(JSON.stringify(data,  null, 2 ));
//                 console.log(orderId,JSON.stringify(data,  null, 2 ));
//             },
//             error: function (xhr, ajaxOptions, thrownError) {
//               showServicePopupWindow(
//                             "Ошибка " + xhr.status,
//                             "Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError
//                             );
//                 return false;
//             }
//         });
// }

$(document).ready(function () {
    if ($('#profile-order').length){
      var clientOrders = new Vue({
        el: '#profile-order',
        data: {
          order: [],
          products: [],
          orderId: ''
        },
        computed: {
          totalPrice(){
            let price = 0,
                oldprice = 0;
            for (delivery of this.order.deliveryInfo.deliveries){
              for (requisition of delivery.requisitions){
                for (product of requisition.products) {
                  price += (product.price - product.price * product.discount / 100);
                  oldprice += +product.price;
                  console.log(price/100,oldprice/100);
                }
              }
            }
            return {
              price: price / 100,
              oldprice: oldprice / 100
            }
          },
        },
        methods: {
          isToday(someDate) {
              const today = new Date();
              return someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
          },
          getLocaledTime(time){
            let localedDate = '';
            let orderDate = new Date(time * 1000);
            if (this.isToday(orderDate)){
              localedDate = 'сегодня';
            } else {
              localedDate = orderDate.toLocaleDateString('ru-RU', {day: "numeric", month: "long", year: "numeric"});
            }
            localedTime = ' в ' + orderDate.toLocaleTimeString('ru-RU', {hour: "numeric", minute: "numeric"});
            return localedDate + localedTime;
          },
          getOrder(){
            axios
              .post('/ajax-get-order-page',
                Qs.stringify({
                  orderId : this.orderId
              }))
              .then(response => {
                console.log(response);
                this.order = response.data.order_info[0];
                this.products = response.data.productsMap;
                console.log('заказ',this.order);
              });
          },
          isToday(someDate) {
              const today = new Date();
              return someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
          }
        },
        created() {
          this.orderId = window.location.href.split("/").slice(-1)[0];
          this.getOrder();
        }
      });
    }
});
