/* 
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

$(document).ready(function () {

});

$(function () {

    var sendSms = function(phone) {
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
            data: data, // formData,
            success: function (result, status, xhr) {
                console.log('result = ', result, 'status = ', status);
            },
            error: function (xhr, status, error) {
                console.log('Sms sending failed', xhr, status);
            }
        });
    };
    
    var sendBack = function(code) {
        data = {'code': code };
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
    
    var setClientInfo = function(params) {
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
    
    var getClientInfo = function(params) {
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
    
    var updateClientInfo = function(params) {
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

    var changeClientPassword = function(params) {
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

    $('#userDataFormId').click(function(){
        sendSms($('#inputSomeDataId').val());
    });
    $('#codeFeedbackId').click(function(){
        sendBack(7777);
    });
    
    $('#setClientInfoId').click(function(){
        setClientInfo({'name': 'name1', 'surname': 'surname1', 'middle_name': 'middle_name2', 'phone': $('#inputSomeDataId').val(), 'email': 'my@mymail.ru'});
    });

    $('#getClientInfoId').click(function(){
        getClientInfo({'id': $('#inputSomeDataId').val()});
    });

    $('#updateClientInfoId').click(function(){
        updateClientInfo({'id': $('#inputSomeDataId').val(),'name': 'name2', 'surname': $('#inputSomeDataId').val(), 'middle_name': 'middle_name2', 'phone': '9185356024', 'email': 'my1@mymail.ru'});//9185356025
    });

    $('#changeClientPasswordId').click(function(){
        console.log('id = ', $('#inputSomeDataId').val());
        changeClientPassword({'id': $('#inputSomeDataId').val(),'old_password': '4uemOAs', 'new_password': 'newpassword', 'new_password2': 'newpassword'});
    });
});