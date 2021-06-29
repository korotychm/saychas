$(function(){
    var panel = $('#controlPanelMenu li a');
    panel.unbind();
    
    panel.click(function(ths){
        $('#controlPanelMenu li a.active').removeClass('active');
        $('#' + ths.currentTarget.id).addClass('active');
        
        if('storesId' === ths.currentTarget.id){
            $.post('/control-panel/show-stores', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut("fast", function(){
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function(){
                    $('table tr.line').unbind();
                    $('table tr.line').click(function(ths){
                        $('table tr.active').removeClass('active');
                        $('#'+ths.currentTarget.id).addClass(('active'));
                        
                            var id = ths.currentTarget.id;
                            console.log('ths = ', id);
                            var getForm = function(id) {
                                data = {'id': id };
                                $.ajax({
                                    type: "POST",
                                    url: "/control-panel/show-one-store/"+id,
                                    // method: 'get',
                                    // data: data,
                                    success: function (result, status, xhr) {
                                        console.log('result = ', result, 'status = ', status);
                                        $('.row-content').html();
                                        $('.row-content').html(result);
                                    },
                                    error: function (xhr, status, error) {
                                        console.log('Sms sending failed', xhr, status);
                                    }
                                });
                            };
                            getForm(id);

                        
                    });
                });
            })
            .fail(function(data){
                console.log('ShowMenus failed :( data = ', data, ' ', data.statusText);
            });
        }else if('productsId' === ths.currentTarget.id){
            $.post('/control-panel/show-products', {post: {}}, function (data) {
                $('#controlPanelContentId').fadeOut('fast', function(){
                    $('#controlPanelContentId').html("");
                    $('#controlPanelContentId').html(data);
                });
                $('#controlPanelContentId').fadeIn("fast", function(){
                    
                });
            })
            .fail(function(data){
                console.log('ShowMenus failed :( data = ', data, ' ', data.statusText);
            });            
        }
    });
    $('#storesId').click();
});