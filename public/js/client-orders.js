// function getClientOrders(){
//     $.ajax({
//             beforeSend: function () {},
//             url: "/ajax-get-order-list",
//             type: 'POST',
//             cache: false,
//             success: function (data) {
//                 //if(data.result == 1)
//                 $("#order-list").html(JSON.stringify(data, null, 2 ));
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
//
// $(document).ready(function () {
//     getClientOrders();
// });

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
          order.total = 0;
          order.oldtotal = 0;
          order.count = 0;
          let orderDate = new Date(+order.orderDate * 1000);
          if (this.isToday(orderDate)){
            order.dateLocaled = 'сегодня';
          } else {
            order.dateLocaled = orderDate.toLocaleDateString('ru-RU', {day: "numeric", month: "long", year: "numeric"});
          }
          order.timeLocaled = orderDate.toLocaleTimeString('ru-RU', {hour: "numeric", minute: "numeric"});

          // order.deliveryInfo.pickup = JSON.parse(JSON.stringify(order.deliveryInfo.delivery_info.deliveries));
          // order.deliveryInfo.pickup = order.deliveryInfo.pickup.filter((delivery) => {
          //   return (delivery.pickup == true)
          // })


          // order.deliveryInfo.delivery_info.deliveries = JSON.parse(JSON.stringify(order.deliveryInfo.delivery_info.deliveries));
          // order.deliveryInfo.delivery_info.deliveries = order.deliveryInfo.delivery_info.deliveries.filter((delivery) => {
          //   return (delivery.pickup == false)
          // })

          // let pickupCount = order.deliveryInfo.pickup.length;
          // if (pickupCount == 1){
          //   order.pickupUnit = 'магазина';
          // } else {
          //   order.pickupUnit = 'магазинов';
          // }

          let deliveryCount = order.deliveryInfo.delivery_info.deliveries.length;
          if (deliveryCount == 1){
            order.deliveryUnit = 'доставка';
          } else if (deliveryCount > 1 && deliveryCount < 5) {
            order.deliveryUnit = 'доставки';
          } else {
            order.deliveryUnit = 'доставок';
          }

          if (order.basketInfo.ordermerge && order.basketInfo.timepoint){
            order.basketInfo.timepoint = order.basketInfo.timepoint[+order.basketInfo.ordermerge];
            let deliveryTime = 1;
            if (+order.basketInfo.ordermerge == 1){
              deliveryTime = 3;
            }
            if (order.basketInfo.timepoint !== '0'){
              order.timepoint = [+order.basketInfo.timepoint, +order.basketInfo.timepoint + deliveryTime];
            } else {
              order.timepoint = [orderDate.getHours() + 1, orderDate.getHours() + 1 + deliveryTime];
            }
            if (order.timepoint[1] > 24){
              order.timepoint[1] = order.timepoint[1] - 24;
            }
            else if (order.timepoint[1] == 24){
              order.timepoint[1] = 0;
            }
            if (order.timepoint[0] < 10){
              order.timepoint[0] = '0' + order.timepoint[0];
            }
            if (order.timepoint[1] < 10){
              order.timepoint[1] = '0' + order.timepoint[1];
            }
          }

          // for (delivery of order.deliveryInfo.delivery_info.deliveries){
          //   if (delivery.requisitions){
          //     for (requisition of delivery.requisitions){
          //       for (product of requisition.items){
          //         product.img = this.products[product.id].image;
          //         product.title = this.products[product.id].title;
          //         order.deliveryProducts.push(product);
          //         order.total += (product.price / 100);
          //         if (+product.discount > 0) {
          //           order.oldtotal += (product.price / (100 - product.discount));
          //         } else {
          //           order.oldtotal += (product.price / 100);
          //         }
          //         order.count++;
          //       }
          //     }
          //   }
          // }
          //
          // for (delivery of order.deliveryInfo.pickup){
          //   if (delivery.requisitions){
          //     for (requisition of delivery.requisitions){
          //       for (product of requisition.items){
          //         product.img = this.products[product.id].image;
          //         product.title = this.products[product.id].title;
          //         order.pickupProducts.push(product);
          //         order.total += (product.price / 100);
          //         if (+product.discount > 0) {
          //           order.oldtotal += (product.price / (100 - product.discount));
          //         } else {
          //           order.oldtotal += (product.price / 100);
          //         }
          //         order.count++;
          //       }
          //     }
          //   }
          // }

          order.productsTotal = order.total;
          order.oldProductsTotal = order.oldtotal;
          if (order.basketInfo.delivery_price){
            order.total += (order.basketInfo.delivery_price / 100);
            order.oldtotal += (order.basketInfo.delivery_price / 100);
          }
        }
        console.log('prepared', orders);
        return orders;
      }
    },
    methods: {
      totalItems(index){
        let itemsTotal = 0;
        for (delivery of this.preparedOrders[index].deliveryInfo.delivery_info.deliveries){
          for (requisition of delivery.requisitions){
            for (product of reuisitions.items){
              itemsTotal++;
            }
          }
        }
        return {
          itemsTotal
        }
      },
      totalPrice(index){
        let price = 0,
            oldprice = 0;
        for (delivery of this.preparedOrders[index].deliveryInfo.delivery_info.deliveries){
          for (requisition of delivery.requisitions){
            if (requisition.status_id != 5){ // Заявка не отменена
              for (product of requisition.items) {
                price += ((product.price - product.price * product.discount / 100) * product.qty_fact);
                oldprice += (product.price * product.qty_fact);
              }
            }
          }
        }
        return {
          price: price / 100,
          oldprice: oldprice / 100
        }
      },
      getClientOrders() {
        axios
          .post('/ajax-get-order-list')
          .then(response => (
            this.orders = response.data.order_list,
            this.products = response.data.productsMap,
            console.log('Список заказов', response)
          ));
      },
      isToday(someDate) {
          const today = new Date();
          return someDate.getDate() == today.getDate() &&
            someDate.getMonth() == today.getMonth() &&
            someDate.getFullYear() == today.getFullYear()
      }
    },
    mounted() {
      this.getClientOrders()
    }
  });

});
