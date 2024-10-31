jQuery(function($){

 $( '.retailer_info #submit' ).on('click',function(e){
        e.preventDefault();
        $( '.retailer_info_success , .retailer_info_error' ).remove();
        var clicked = $( this );
        $( clicked ).css('pointer-events' , 'none');
        $( '.submit_preloader' ).css('display' , 'inline');

     
        //$( clicked ).css( 'pointer-events' , 'none' );
        //var checked = $( this ).siblings('#togBtn').attr('checked');
        var datas = {
                'action': 'parvenu_update_retailer_info',
                'parv_update_retailer_info_nonce': $( '#parv_update_retailer_info_nonce' ).val(),
                'post': $('.retailer_info form').serializeObject()
            };
     
         $.ajax({
            type: 'POST',
            url: parvenu_admin_obj.ajax_url,
            data: datas,
            success: function( feedback ) {
                if (feedback.success == 1) {
                    $('.retailer_info form').append( $( '<div class="retailer_info_success">'+feedback.message+'</div>' ) );   
                }else {
                    var errors = '<ul class="retailer_vald_errors">';
                    $(feedback.message).each(function(index, value){
                        errors += '<li>'+value+'</li>';   
                    }); 
                    errors += '</ul>';
                    $('.retailer_info form').prepend( $( '<div class="retailer_info_error">'+errors+'</div>' ) );  
                }
                $( clicked ).css('pointer-events' , 'auto');
                $( '.submit_preloader' ).css('display' , 'none');
                $('.parvenu_fill_info_notice').css('display' , 'none');
            },
            error: function( xhr,status,error ) {
                console.log(status);     
                
            }
        }); 
        
    });
    
    
    $.fn.serializeObject = function()
    {
       var o = {};
       var a = this.serializeArray();
       $.each(a, function() {
           if (o[this.name]) {
               if (!o[this.name].push) {
                   o[this.name] = [o[this.name]];
               }
               o[this.name].push(this.value || '');
           } else {
               o[this.name] = this.value || '';
           }
       });
       return o;
    };
    
});