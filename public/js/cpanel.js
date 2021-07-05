$(function () {
    
    /** calendar loaded; lets add some event handlers */
//    $(document).on('calendarLoaded', function(e, data){
//        $('#calendar-left').unbind();
//        $('#calendar-left ul.days li span').click(function(ths){
//            $('li span').removeClass('active');
//            $(ths.target).addClass('active');
//            // we need to load apropriate screen below
//            $('#calendar-right').html(ths.target.id);
//        });
//        $('li span.active').click();
//    });
//
    $(document).on('calendarLoaded', function(e, data){
        $('#calendar-left').unbind();
        $('#calendar-left ul.days li span').click(function(ths){
            $('li span').removeClass('active');
            $(ths.target).addClass('active');
            // we need to load apropriate screen below
            //$('#calendar-right').html($(ths.target).html());
            $.post('/control-panel/calendar-details', {'day': $(ths.target).html(), 'month': $(ths.target).attr('month'), 'year': $(ths.target).attr('year')}, function (data) {
                $('#calendar-right').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#calendar-right').html("");
                    $('#calendar-right').html(data);
                });
                $('#calendar-right').fadeIn("fast", function () {
                });
            })
            .fail(function (data) {
                console.log('Calendar failed to load :( data = ', data, ' ', data.statusText);
            });

        });
        /** click a calendar day */
        // $('li span.active').click();
    });

    var redirectIfNotLoggedIn = function (result) {
        if ('null' === result) {
            window.location.replace("/control-panel");
        }
    };
    var panel = $('#controlPanelMenu li a');
    panel.unbind();

    panel.click(function (ths) {
        $('#controlPanelMenu li a.active').removeClass('active');
        $('#' + ths.currentTarget.id).addClass('active');

        if ('profileId' === ths.currentTarget.id) {
            $.post('/control-panel/profile', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function () {
                });
            })
            .fail(function (data) {
                console.log('Show Profile failed :( data = ', data, ' ', data.statusText);
            });
        } else if('accountManagementId' === ths.currentTarget.id) {
            $.post('/control-panel/account-management', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function () {
                });
            })
            .fail(function (data) {
                console.log('Show Profile failed :( data = ', data, ' ', data.statusText);
            });
        } else if ('storesId' === ths.currentTarget.id) {
            $.post('/control-panel/show-stores', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                    /** calendar: trigger calendarLoaded event */
                    $(document).trigger('calendarLoaded');
                });
                $('#controlPanelContentId').fadeIn("fast", function () {
                    $('table tr.line').unbind();
                    $('table tr.line').click(function (ths) {
                        $('table tr.active').removeClass('active');
                        $('#' + ths.currentTarget.id).addClass(('active'));

                        var id = ths.currentTarget.id;
                        var getForm = function (id) {
                            data = {'id': id};
                            $.ajax({
                                type: "POST",
                                url: "/control-panel/show-one-store/" + id,
                                success: function (result, status, xhr) {
                                    redirectIfNotLoggedIn(result);
                                    $('.row-content').html();
                                    $('.row-content').html(result);
                                },
                                error: function (xhr, status, error) {
                                    console.log('Store table content', xhr, status);
                                }
                            });
                        };
                        getForm(id);
                    });
                });
            })
            .fail(function (data) {
                console.log('ShowMenus failed :( data = ', data, ' ', data.statusText);
            });
        }else if('actionAndDiscountId' === ths.currentTarget.id) {
            $.post('/control-panel/action-and-discount', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function () {
                });
            })
            .fail(function (data) {
                console.log('Show Profile failed :( data = ', data, ' ', data.statusText);
            });
        }else if('respondingToReviewsId' === ths.currentTarget.id) {
            $.post('/control-panel/responding-to-reviews', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function () {
                });
            })
            .fail(function (data) {
                console.log('Show Profile failed :( data = ', data, ' ', data.statusText);
            });
        } else if ('productsId' === ths.currentTarget.id) {
            $.post('/control-panel/show-products', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut('fast', function () {
                    redirectIfNotLoggedIn(data);
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function () {

                });
            })
            .fail(function (data) {
                console.log('ShowProducts failed :( data = ', data, ' ', data.statusText);
            });
        }
    });
    $('#storesId').click();
});