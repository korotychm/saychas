// $('.testdiv').keydown(function(e){
//   console.log('down');
//   if (e.keyCode == 40){
//     $('input[type="radio"]').eq(0).focus();
//   }
// });
//
// $('input[type="radio"]').keydown(function(e){
//   e.preventDefault();
//   let activeEl = $('input[type="radio"]:focus'),
//       index = activeEl.parent().index() - 1,
//       length = $('input[type="radio"]').length,
//       step = 0;
//   if (e.keyCode == 13){
//     $('input[type="radio"]:focus').prop('checked',true);
//     return;
//   }
//   if (e.keyCode == 40){
//     index++;
//     if (index == length){
//       index = 0;
//     }
//   }
//   if (e.keyCode == 38){
//     index--;
//     if (index == -1){
//       index = length - 1;
//     }
//   }
//   console.log('pressed',index);
//   $('input[type="radio"]').eq(index).focus();
// })
