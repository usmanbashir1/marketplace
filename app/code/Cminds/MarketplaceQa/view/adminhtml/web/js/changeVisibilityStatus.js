require(['jquery'], function($){
    $('.qa_visibility').change(function() {
    	var dataId = $(this).attr('data-colid');
    	var qaUrl = $(this).attr('data-requesturi');
    	var value = $(this).val();
        $.ajax({
            url: qaUrl, 
            method: "post", 
            dataType: "json", 
            data:{
              id: dataId,
              value: value,
            },
            success: function(response){
            }
        });
    })
});