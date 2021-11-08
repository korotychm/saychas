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
    //getClientOrder();
    if ($('#profile-order').length){
      var clientOrders = new Vue({
        el: '#profile-order',
        data: {
          order: [],
          products: [],
          orderId: ''
        },
        methods: {
          getOrder(){
            axios
              .post('/ajax-get-order-page',
                Qs.stringify({
                  orderId : this.orderId
              }))
              .then(response => {
                console.log(response);
                this.order = response.data;
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
          this.getOrder()
        }
      });
    }
});
