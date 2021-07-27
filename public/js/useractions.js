function sendauthformmodal (data = false){
        if(!data) data = $("#userAuthModalForm").serialize();
         $.ajax({
            beforeSend : function (){ 
                $("#user-modal-cover").stop().fadeIn(); 
                },
            url: "/user-auth-modal",
            type: 'POST',
            cache: false,
            data: data,
            success: function (data) {
                if(data.reload) {
                    console.log(data);
                    location = location.href; return false 
                };
                $("#user-modal-cover").stop().hide();
                //console.log(data);
                
                $("#userAuthModalForm").html(data);
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Ошибка соединения, попробуйте повторить попытку позже." + "\r\n " + xhr.status + " " + thrownError + "");
                $("#user-modal-cover").stop().hide();
                return false;
            }
        });
    return false;    
 }

$(document).ready(function () {
    
    
    $("#userAuthModalForm").on("click","#goStepOne", function(){
        sendauthformmodal ({"goStepOne":"1"});
    })
    $("#userAuthModalForm").on("focus",".lableinfo", function(){
        $("#userAuthModalForm * ").removeClass("error");
        //$("#userAuthModalForm .errormessage ").remove();
    })
    
    $("#userAuthModalForm").submit(function(){
        sendauthformmodal ();
        return false;
    })  
})

