/* 
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

$(document).ready(function () {

});

$(function () {
//    $.ajax({
//        beforeSend : function (){ 
//            console.log('before send');
//        },
//        url: "/send-registration-sms",
//        type: 'POST',
//        cache: false,
//        data: dataString,
//        success: function (data) {
//
//            $("#ajaxfiltranswer").html(data);
//            window.location.href = window.location.href
//            //alert("!!!!234");
//        },
//        error: function (xhr, ajaxOptions, thrownError) {
//            $("#ajaxfiltranswer").html("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError);
//            alert(("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError));
//        }
//    });

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
    
    $('#userDataFormId').click(function(){
        sendSms(9185356024);
    });
    $('#codeFeedbackId').click(function(){
        sendBack(7777);
    });
    
    $('#setClientInfoId').click(function(){
        setClientInfo({'name': 'name1', 'surname': 'surname1', 'middle_name': 'middle_name2', 'phone': 9185356024});
    });

    $('#getClientInfoId').click(function(){
        getClientInfo({'phone': 9185356024});
    });
});