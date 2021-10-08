/*
 * Here comes the text of your license
 * Each line should be prefixed with  *
 */

$(document).ready(function () {

});

$(function () {

    var sendSms = function (phone) {
//        var formData = new FormData();
//        formData.append('phone', '9185356024');
//        formData.append('code', '7777');
        data = {'phone': phone};
        $.ajax({
            type: "POST",
            url: "/send-registration-sms",
            //dataType: "json",
            method: 'post',
//            contentType: false, // Not to set any content header
//            processData: false, // Not to process data
            data: JSON.stringify(data), // formData,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };

    var setAveragePrice = function (categoryId) {
        var data = {'categoryId': categoryId};
        //console.log(categoryId);
        $.ajax({
            type: "POST",
            url: "/average-category-price",
            //dataType: "json",
            //method: 'post',
//            contentType: false, // Not to set any content header
//            processData: false, // Not to process data
            data: data, // formData,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };

    var sendTinkoff = function () {
//        var formData = new FormData();
//        formData.append('phone', '9185356024');
//        formData.append('code', '7777');
        var data = {"TerminalKey": "1629956533317DEMO",
            "OrderId": "000000566",
            "Success": true,
            "Status": "CONFIRMED",
            "PaymentId": 699599295,
            "ErrorCode": "0",
            "Amount": 229900,
            "CardId": 99866533,
            "Pan": "430000******0777",
            "ExpDate": "1122",
            "Token": "08e3718b7790f24a2984d048526a8bfde97cb3de39c14af839d45d6d83eab5ed"};
        $.ajax({
            type: "POST",
            url: "/tinkoff/callback",
            //processData: false,
            contentType: "application/json; charset=UTF-8",
            dataType: "json",
            //contentType: "application/json",
//            contentType: false, // Not to set any content header
//            processData: false, // Not to process data
            data: JSON.stringify(data),
            // complete: callback,// formData,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status /*, 'xhr = ', xhr */);
            },
            error: function (xhr, status, error) {
                console.log('Test sending failed', xhr, status);
            }
        });
    };

    var sendBack = function (code) {
        data = {'code': code};
        $.ajax({
            type: "POST",
            url: "/send-feedback-code",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };

    var setClientInfo = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/set-client-info",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };

    var getClientInfo = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/get-client-info",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };

    var updateClientInfo = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/update-client-info",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms updating failed', xhr, status);
            }
        });
    };

    var changeClientPassword = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/change-client-password",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms updating failed', xhr, status);
            }
        });
    };

    var getImageFromFtpId = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/get-image",
            method: 'post',
            dataType: 'html',
            async: true,
            data: data,
            success: function (result, status, xhr) {
//                var img = $('<img style="height: 100px; width: 100px;" id="image_id">');
//                img.attr('src', '/hello-world');
//                img.appendTo('#image_div');
                $('#image_div').html(result);
                $('#image_div img').css({'height': '100px', 'width': '200px'});
            },
            error: function (xhr, status, error) {
                console.log('Sms updating failed', xhr, status);
            }
        });
    };

    var clientLogin = function (params) {
        data = params;
        $.ajax({
            type: "POST",
            url: "/client-login",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result);
            },
            error: function (xhr, status, error) {
                console.log('Sms updating failed', xhr, status);
            }
        });
    };

    var sendProductRating = function (params) {
        var data = params;
        $.ajax({
            type: "POST",
            url: "/ajax-set-product-rating",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result);
                $("#productRatingAnswer").html(JSON.stringify(result, null, " "));
            },
            error: function (xhr, status, error) {
                console.log('sendProductRating Faled', xhr, status);
            }
        });
    };

    var getProductReview = function (params) {
        var data = params;
        $.ajax({
            type: "POST",
            url: "/ajax-get-product-review",
            method: 'post',
            data: data,
            success: function (result, status, xhr) {
                console.log('result = ', result);
                $("#productRatingAnswer").html(JSON.stringify(result, null, " "));
            },
            error: function (xhr, status, error) {
                console.log('GetProductReview failed', xhr, status);
            }
        });
    };

    $('#userDataFormId').click(function () {
        sendSms($('#inputSomeDataId').val());
    });

    $('#productRatingForm').submit(function () {
//        console.log("****");
        var data = $('#productRatingForm').serialize();
        // console.log(data);
        sendProductRating(data);
        return false;
    });

    $('#productReview').click(function () {
        var data = $('#productRatingForm').serialize();
        getProductReview(data);
        return false;
    });

    $('#productUserRating').click(function () {
        var data = $('#productRatingForm').serialize();
        getProductUserRating(data);
        return false;
    });

    $('#sendTinkoff').click(function () {
        sendTinkoff();
    });

    $('#sendAveragePrice').click(function () {
        setAveragePrice($('#inputSomeDataId').val());
    });

    $('#codeFeedbackId').click(function () {
        sendBack(7777);
    });

    $('#setClientInfoId').click(function () {
        setClientInfo({'name': 'name1', 'surname': 'surname1', 'middle_name': 'middle_name2', 'phone': $('#inputSomeDataId').val(), 'email': 'my@mymail.ru'});
    });

    $('#getClientInfoId').click(function () {
        getClientInfo({'id': $('#inputSomeDataId').val()});
    });

    $('#updateClientInfoId').click(function () {
        updateClientInfo({'id': $('#inputSomeDataId').val(), 'name': 'name2', 'surname': $('#inputSomeDataId').val(), 'middle_name': 'middle_name2', 'phone': '9185356024', 'email': 'my1@mymail.ru'});//9185356025
    });

    $('#changeClientPasswordId').click(function () {
        console.log('id = ', $('#inputSomeDataId').val());
        changeClientPassword({'id': $('#inputSomeDataId').val(), 'old_password': '4uemOAs', 'new_password': 'newpassword', 'new_password2': 'newpassword'});
    });

    $('#getImageFromFtpId').click(function () {
        console.log('getImageFromFtpId');
        getImageFromFtpId({'table': 'product', 'fileName': '1350x.jpg'});
    });

    $('#clientLogin').click(function () {
        clientLogin({'phone': '9160010204', 'password': '1112233T'});
    });
});