$(function(){
    /** calendar loaded; lets add some event handlers */
//    $(document).on('calendarLoaded', function(e, data){
//        $('#calendar-left').unbind();
//        $('#calendar-left ul.days li span').click(function(ths){
//            $('li span').removeClass('active');
//            $(ths.target).addClass('active');
//            // we need to load apropriate screen below
//            $('#calendar-right').html($(ths.target).html());
//            $.post('/control-panel/calendar-details', {post: {}}, function (data) {
//                alert('okaaa');
//                $('#calendar-right').fadeOut("fast", function () {
//                    redirectIfNotLoggedIn(data);
//                    $('#calendar-right').html("");
//                    $('#calendar-right').html(data);
//                });
//                $('#calendar-right').fadeIn("fast", function () {
//                });
//            })
//            .fail(function (data) {
//                console.log('Calendar failed to load :( data = ', data, ' ', data.statusText);
//            });
//            
//        });
//        $('li span.active').click();
//    });
});