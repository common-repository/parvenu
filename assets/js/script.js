
jQuery(document).ready(function(){
jQuery( window ).load(function() {

    jQuery(".parv_pop_up_close").click(function(e){
        e.preventDefault();
        var checkout_page = php_vars.checkout;
        window.location.href = checkout_page;
    });
    
    jQuery("body").on("click", ".wc-proceed-to-checkout , .wc-proceed-to-checkout a", function(e){
        e.preventDefault();
        jQuery(".parvenu_charity_popup").css("display" , "block");
    });


    jQuery("#parv_add_cart").click(function(e){
        e.preventDefault();
        e.stopPropagation();
        var checkout_page = php_vars.checkout;
        window.location.href = checkout_page+"?add-to-cart="+parseInt(jQuery(".parv-quan input[type='number']").attr('data-product'))+"&quantity="+parseInt(jQuery(".parv-quan input[type='number']").val());
    });
    
    });
    
    jQuery("body").on("click", ".button.checkout.wc-forward", function(event){
        event.preventDefault();
        event.stopPropagation();
        jQuery(".parvenu_charity_popup").css("display" , "block");
    })

});
