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
          totalDeliveries(){
            let deliveriesTotal = 0,
                pickupsTotal = 0
            for (delivery of this.order.deliveryInfo.delivery_info.deliveries){
              if (delivery.pickup){
                pickupsTotal++;
              } else {
                deliveriesTotal++;
              }
            }
            return {
              deliveriesTotal,
              pickupsTotal
            }
          },
          totalPrice(){
            let price = 0,
                oldprice = 0;
            for (delivery of this.order.deliveryInfo.delivery_info.deliveries){
              for (requisition of delivery.requisitions.requisitions){
                for (product of requisition.items) {
                  price += ((product.price - product.price * product.discount / 100) * product.qty_fact);
                  oldprice += (product.price * product.qty_fact);
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
          getTimePointText(ms,timepoint,merged = false) {
              //created - дата заказа
              let deliveryTime = 1; // если обычная доставка за час
              if (merged) deliveryTime = 3; // если объединенная доставка за три часа

              let orderDate = new Date(ms); // Дата заказа
              let timepoints = [];

              if (timepoint !== '0'){
                timepoints = [+timepoint, +timepoint + deliveryTime];
              } else {
                timepoints = [orderDate.getHours() + 1, orderDate.getHours() + 1 + deliveryTime];
              }
              if (timepoints[1] > 24){
                timepoints[1] = timepoints[1] - 24;
                orderDate.setDate(orderDate.getDate() + 1);
              }
              else if (timepoints[1] == 24){
                timepoints[1] = 0;
                orderDate.setDate(orderDate.getDate() + 1);
              }
              if (timepoints[0] < 10){
                timepoints[0] = '0' + timepoints[0];
              }
              if (timepoints[1] < 10){
                timepoints[1] = '0' + timepoints[1];
              }

              if (this.isToday(orderDate)){
                let dateLocaled = 'Сегодня';
              } else {
                let dateLocaled = orderDate.toLocaleDateString('ru-RU', {day: "numeric", month: "long", year: "numeric"});
              }
              return dateLocaled + ' с ' + timepoints[0] + ' до ' + timepoints[1];
          },
          deliveriesUnit(q,isPickup = false){
            q = q.toString();
            if (+q.slice(-1) == 1 && q.slice(-2) != 11){
              if (isPickup) return 'самовывоз';
              return 'доставка';
            }
            if (+q.slice(-1) > 1 && +q.slice(-1) < 5 && q.slice(-2) != 12 && q.slice(-2) != 13 && q.slice(-2) != 14){
              if (isPickup) return 'самовывоза';
              return 'доставки';
            }
            if (isPickup) return 'самовывозов';
              return 'доставок';
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
