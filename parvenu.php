<?php
/*
Plugin Name: Parvenu for Woocommerce
Plugin URI:  https://www.parvenunext.com/
Description: Parvenu helps retailers raise more money for charity and drive engagement through personalization.
Version: 1.6
Author: Parvenu Fundraising, Inc
Text Domain: parvenu
License: GPL
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
define('PARVENU_DIRECTORY' , dirname(__FILE__));
define('PARVENU_DIRECTORY_DIRECTORY' , dirname(dirname(__FILE__)));
define('PARVENU_PLUGIN_URL' , plugin_dir_url( __FILE__ ));

// Action Hooks
register_activation_hook( __FILE__, 'parvenu_activate' );
register_uninstall_hook( __FILE__, 'parvenu_uninstall' );
register_deactivation_hook(__FILE__, 'parvenu_uninstall');

add_action( 'woocommerce_add_to_cart', 'parvenu_woocommerce_add_to_cart', 10, 6 );
add_action( 'wp_enqueue_scripts', 'parvenu_enqueue_wp_styles' );
add_action( 'admin_enqueue_scripts', 'parvenu_enqueue_admin_styles' );
add_action( 'admin_menu', 'parvenu_admin_menu' );
add_action( 'wp_ajax_parvenu_update_retailer_info' , 'parvenu_update_retailer_info' );
add_action('admin_init', 'parvenu_plugin_redirect');
add_action('wp_head', 'google_analytics_header');



function google_analytics_header(){
    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-142141761-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-142141761-1');
        </script>

    <?php
}

// Functions to execute
function parvenu_activate() {
    $parvenu_charity_products = array(
         array(
            'title'         => 'Donate to Doctors Without Borders, USA',
            'description'   => 'Donate to Doctors Without Borders, USA',
            'price'         => 1,
            'charity_id'    => 3,
            'link'          => PARVENU_PLUGIN_URL.'assets/images/Doctors-Without-Borders.png'
        ),
        array(
            'title'         => 'Donate to Save the Children',
            'description'   => 'Donate to Save the Children',
            'price'         => 1,
            'charity_id'    => 4,
            'link'          => PARVENU_PLUGIN_URL.'assets/images/Save_the_Children.jpg'
        ),
        array(
            'title'         => 'Donate to World Wildlife Fund',
            'description'   => 'Donate to World Wildlife Fund',
            'price'         => 1,
            'charity_id'    => 5,
            'link'          => PARVENU_PLUGIN_URL.'assets/images/World-Wildlife-Fund.png'
        ),
    );

    // Add parvenu charity product to store for the first time

        // Check if woocommerce Plugin is active then activate, else otherwise
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            die('Woocommerce Plugin must be activated to use this add on'); exit();
        }

        foreach ($parvenu_charity_products as $charity_product) {
            if ( parvenu_check_if_charity_product( $charity_product['charity_id'] ) == true ) {
                continue;
            }
            $product_id = parvenu_create_default_charity_products( $charity_product['title'] , $charity_product['description'] , $charity_product['price'] , $charity_product['link'] );
            update_post_meta( $product_id, 'charity_id', $charity_product['charity_id'] );

            $product = wc_get_product($product_id);
            $product->set_catalog_visibility('hidden');
            $product->save();
        }
        add_option('parvenu_do_activation_redirect', true);

    // Save default retailer details
    $parvenu_retailer_info = get_option( 'parvenu_retailer_info' );

    if ( !$parvenu_retailer_info ){
       $store_admin = get_user_by('id', 1);
       $parvenu_retailer_info = array(
            'first_name'   => get_user_meta( 1, 'billing_first_name', true ) ? get_user_meta( 1, 'billing_first_name', true ) : '',
            'last_name'    => get_user_meta( 1, 'billing_last_name', true ) ? get_user_meta( 1, 'billing_last_name', true ) : '',
            'email'        => get_user_meta( 1, 'billing_email', true ) ? get_user_meta( 1, 'billing_email', true ) : get_option('admin_email'),
            'address'      => get_user_meta( 1, 'billing_address_1', true ) ? get_user_meta( 1, 'billing_address_1', true ) : '',
            'company'      => get_option( 'blogname' ) ? get_option( 'blogname' ) : '',
            'city'         => get_user_meta( 1, 'billing_city', true ) ? get_user_meta( 1, 'billing_city', true ) : '',
            'state'        => get_user_meta( 1, 'billing_phone', true ) ? get_user_meta( 1, 'billing_phone', true ) : '',
            'zip'          => get_user_meta( 1, 'billing_postcode', true ) ? get_user_meta( 1, 'billing_postcode', true ) : '',
            'website'          => get_user_meta( 1, 'billing_website', true ) ? get_user_meta( 1, 'billing_website', true ) : ''
           
       );
       add_option( 'parvenu_retailer_info' , $parvenu_retailer_info );
    }
}

function parvenu_plugin_redirect() {
    if (get_option('parvenu_do_activation_redirect', false)) {
        delete_option('parvenu_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect(admin_url('/admin.php?page=parvenu_admin'));
        }
    }
}

function parvenu_woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
    if(isset($_COOKIE['parvenu_charity_id'])) {
        //return;
    }
    // Get the product data using the product id, you can get further details of the item being added to cart from the object.
    $product = wc_get_product( $product_id );
    $url = 'https://parvenurecw2v-intelligent-swan.mybluemix.net/parvenu/api/rec';
    $data = array('id' => $product_id);
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result != FALSE) {
        $parvenu_charity_id = json_decode($result, true);
        $cookie_name = "parvenu_charity_id";
        $cookie_value = $parvenu_charity_id['charity_id'];
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
    }

}

function parvenu_uninstall() {
    delete_option( 'parvenu_retailer_info' );
    delete_option( 'parvenu_do_activation_redirect' );

    $charity_products = parvenu_get_all_charity_products();
    if( $charity_products != false ) {
        foreach ($charity_products as $charity_product) {
            wp_delete_post((int)$charity_product['post_id']);
        }
    }
}

function parvenu_get_all_charity_products() {
    global $wpdb;
    $result = $wpdb->get_results( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'charity_id'", ARRAY_A );
    if ($result) {
        return $result;
    }else {
        return false;
    }
}

function parvenu_check_if_charity_product($charity_id = 1) {
    global $wpdb;
    $result = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'charity_id' AND meta_value = '{$charity_id}'", ARRAY_A );
    if ($result) {
        return true;
    }else {
        return false;
    }
}

function parvenu_show_charity_on_cart() {
    if (!isset($_COOKIE['parvenu_charity_id']) ) {
        return;
    }
    $product_id = parvenu_get_charity_product_by_metakeyvalue();
    $product = wc_get_product( $product_id[0] );

    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();

    $charity_popup = '<div class="parvenu_charity_popup">';
    $charity_popup .= '<div class="parvenu_charity_popup-inner">';
    $charity_popup .= '<div class="parvenu_charity_popup-content">';
    $charity_popup .= '<span class="parv_pop_up_close">x</span>';
    $charity_popup .= '<div class="parvenu_charity_popup-content-innr">';
    $charity_popup .= '<h3 class="parv-h3">'.$product->get_name().'</h3>';
    $charity_popup .= '<p class="parv-desc">'.$product->get_description().'</p>';
    $charity_popup .= '<div class="parv-img"><img src="'.get_the_post_thumbnail_url( $product->get_id(), "full" ).'" /></div>';
    $charity_popup .= '<p class="parv-prc">'.wc_price($product->get_price()).'</p>';
    $charity_popup .= '<div class="parv-quan"><input type="number" name="quantity" min="1" max="100" placeholder="1" value="1" data-product="'.$product->get_id().'"><button type="submit" class="button" name="parv_add_cart" id="parv_add_cart" value="Add to Cart">Add to Cart</button></div>';
    $charity_popup .= '<div class="parv-proce"><a href="'.$checkout_url.'" name="parv_pro_check" id="parv_pro_check_bottom"><button class="button" value="Proceed to Checkout"> Proceed to Checkout</button></a></div>';
    $charity_popup .= '</div>';
    $charity_popup .= '</div>';
    $charity_popup .= '</div></div>';

       
    echo $charity_popup;
     

}
add_action('wp_footer' , 'parvenu_show_charity_on_cart');


function parvenu_create_default_charity_products( $title='' , $description='' , $price='' , $img_url ='' ) {
    $objProduct = new WC_Product();
    $objProduct->set_name($title);
    $objProduct->set_status("publish");
    $objProduct->set_description($description);
    $objProduct->set_regular_price($price);
    //$objProduct->set_category_ids(array(1,2,3));

    $productImagesIDs = array(); // define an array to store the media ids.
    $images = array($img_url); // images url array of product
    foreach($images as $image){
        $mediaID = uploadMedia($image); // calling the uploadMedia function and passing image url to get the uploaded media id
        if($mediaID) $productImagesIDs[] = $mediaID; // storing media ids in a array.
    }
    if($productImagesIDs){
        $objProduct->set_image_id($productImagesIDs[0]); // set the first image as primary image of the product

            //in case we have more than 1 image, then add them to product gallery.
        if(count($productImagesIDs) > 1){
            $objProduct->set_gallery_image_ids($productImagesIDs);
        }
    }

    $product_id = $objProduct->save();

    return $product_id;
}

function uploadMedia($image_url){
    require_once(ABSPATH.'wp-admin/includes/image.php');
    require_once(ABSPATH.'wp-admin/includes/file.php');
    require_once(ABSPATH.'wp-admin/includes/media.php');
    $media = media_sideload_image($image_url,0);
    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'post_status' => null,
        'post_parent' => 0,
        'orderby' => 'post_date',
        'order' => 'DESC'
    ));
    return $attachments[0]->ID;
}

function parvenu_get_charity_product_by_metakeyvalue() {
    global $wpdb;
    $fetch_charity = $_COOKIE['parvenu_charity_id'];
    $product_id = $wpdb->get_row( "select post_id from $wpdb->postmeta where meta_key = 'charity_id' and meta_value = '$fetch_charity'", ARRAY_N );

    return $product_id;
}

function parvenu_enqueue_wp_styles() {
    if (!isset($_COOKIE['parvenu_charity_id']) ) {
        return;
    }

    wp_enqueue_style( 'parvenu_css', PARVENU_PLUGIN_URL. 'assets/css/style.css' );
    wp_enqueue_script( 'parvenu_js', PARVENU_PLUGIN_URL. 'assets/js/script.js', array( 'jquery' ) , '1.1', true );

     global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    $data = array("checkout" => $checkout_url);

    wp_localize_script("parvenu_js", "php_vars", $data);

}

function parvenu_enqueue_admin_styles() {
    $screen = get_current_screen();
    if ( $screen->id != 'toplevel_page_parvenu_admin' ) {
        return;
    }
    wp_enqueue_style( 'parvenu_admin_css', PARVENU_PLUGIN_URL. 'assets/css/admin_style.css', false, "0.0.1", "all");
    wp_enqueue_script( 'parvenu_admin_js', PARVENU_PLUGIN_URL. 'assets/js/admin_script.js', array( 'jquery' ) , '1.0',true );
   

    
    wp_localize_script('parvenu_admin_js' , 'parvenu_admin_obj' , array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));
    wp_localize_script('parvenu_react_js' , 'parvenu_admin_obj' , array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));
}

function parvenu_admin_menu() {
    add_menu_page( 'Parvenu', 'Parvenu', 'manage_options', 'parvenu_admin', 'parvenu_admin_dashboard', PARVENU_PLUGIN_URL.'assets/images/parvenu.png', '191' );
}

function parvenu_admin_dashboard() {
    echo __( '<h2>Parvenu Dashboard</h2>' , 'parvenu' );

    require_once PARVENU_DIRECTORY.'/admin/retailer_info.php';
}

function parvenu_update_retailer_info() {
    if ( !current_user_can( 'manage_options' ) ) {
        die( __( 'Cheating uhn?' , 'parvenu' ) );
    }

    if( empty( $_POST['action'] ) ||  empty( $_POST['parv_update_retailer_info_nonce'] ) ) {
        $feedback = array(
            'success' => 0,
            'message' =>  __( 'This is not from a trusted source' , 'parvenu' )
        );
        wp_send_json($feedback);
    }

    check_ajax_referer('parvenu_nonce' , 'parv_update_retailer_info_nonce');
    unset($_POST['action']);
    unset($_POST['parv_update_retailer_info_nonce']);
    unset($_POST['post']['parv_update_retailer_info_nonce']);
    unset($_POST['post']['_wp_http_referer']);

    foreach ($_POST['post'] as $key=>$value) {
        $retailer_info[$key] = sanitize_text_field( $value );
    }
    $error = array();

    if (empty($retailer_info['first_name'])) {
        $error[] = __( 'First Name field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['last_name'])) {
        $error[] = __( 'Last Name field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['email'])) {
        $error[] = __( 'Email field can not be empty' , 'parvenu' );
    }elseif( !is_email($retailer_info['email']) ){
        $error[] = __( 'Email is invalid' , 'parvenu' );
    }

    if (empty($retailer_info['address'])) {
        $error[] = __( 'Address field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['company'])) {
        $error[] = __( 'Company field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['city'])) {
        $error[] = __( 'City field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['state'])) {
        $error[] = __( 'State field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['last_name'])) {
        $error[] = __( 'Last Name field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['zip'])) {
        $error[] = __( 'Zip field can not be empty' , 'parvenu' );
    }

    if (empty($retailer_info['website'])) {
        $error[] = __( 'Website field can not be empty' , 'parvenu' );
    }

    if( ! empty($error) ) {
        $feedback = array(
            'success' => 0,
            'message' => $error
        );
        wp_send_json($feedback);
    }else{
        update_option( 'parvenu_retailer_info' , $retailer_info );

       
            $endpoint = 'https://parvenurecw2v-intelligent-swan.mybluemix.net/parvenu/api/retailer';
            // Wordpress default HTTP POST args
            $data = array(
                    'BillingFirstName'        =>    ( !empty( $retailer_info['first_name'] ) ) ? $retailer_info['first_name'] : '',
                    'BillingLastName'         =>    ( !empty( $retailer_info['last_name'] ) ) ? $retailer_info['last_name'] : '',
                    'BillingEmail'             =>    ( !empty( $retailer_info['email'] ) ) ? $retailer_info['email'] : '',
                    'BillingAddress1'           =>    ( !empty( $retailer_info['address'] ) ) ? $retailer_info['address'] : '',
                    'BillingCompany'           =>    ( !empty( $retailer_info['company'] ) ) ? $retailer_info['company'] : '',
                    'BillingCity'              =>    ( !empty( $retailer_info['city'] ) ) ? $retailer_info['city'] : '',
                    'BillingState'             =>    ( !empty( $retailer_info['state'] ) ) ? $retailer_info['state'] : '',
                    'BillingZip'               =>    ( !empty( $retailer_info['zip'] ) ) ? $retailer_info['zip'] : '',
                    'Website'          =>    ( !empty( $retailer_info['website'] ) ) ? $retailer_info['website'] : '',
                    'Time'              =>  gmdate("Y-m-d H:i:s")
            );

            // Send the data to the specified endpoint
            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => json_encode($data)
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($endpoint, false, $context);


        $feedback = array(
            'success' => 1,
            'message' =>  __( 'Your details have been saved successfully' , 'parvenu' )
        );
        wp_send_json($feedback);
    }


}

function parvenu_admin_notice() {
   $parvenu_retailer_info = get_option( 'parvenu_retailer_info' );
   $parvenu_retailer_info = (array)$parvenu_retailer_info;
   
    if ( !$parvenu_retailer_info || $parvenu_retailer_info = '' ){
        echo parvenu_admin_notice_content();
    }else {
        $retailer_info_values = array_values($parvenu_retailer_info);
        if (in_array( '' , $retailer_info_values)) {
            echo parvenu_admin_notice_content();
        }
    }
}
add_action( 'admin_notices', 'parvenu_admin_notice' );

function parvenu_admin_notice_content() {
    $parvenu_retailer_info_page = admin_url('/admin.php?page=parvenu_admin');
    $content = '
        <div class="notice notice-success parvenu_fill_info_notice" style="border-left-color:#0d4ead;">
            <p>'.__( "!important, Please fill in your parvenu retailer details <a href='{$parvenu_retailer_info_page}'>Here</a>", "parvenu" ).'</p>
        </div>
    ';
    return $content;
}
//woocommerce_thankyou
add_action( 'woocommerce_thankyou', 'woo_payment_complete_parvenu' );
function woo_payment_complete_parvenu( $order_id ){
    $order = new WC_Order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {

        $product = wc_get_product( $item['product_id'] );
        $product_price = ($item['qty']) * ($product->get_price());

        if (get_post_meta( $item['product_id'], 'charity_id', true )) {
            $retailer_info = get_option( 'parvenu_retailer_info' );

            $endpoint = 'https://parvenurecw2v-intelligent-swan.mybluemix.net/parvenu/api/donation';
            // Wordpress default HTTP POST args
            $data = array(
                    'BillingFirstName'        =>    ( !empty( $retailer_info['first_name'] ) ) ? $retailer_info['first_name'] : '',
                    'BillingLastName'         =>    ( !empty( $retailer_info['last_name'] ) ) ? $retailer_info['last_name'] : '',
                    'BillingEmail'             =>    ( !empty( $retailer_info['email'] ) ) ? $retailer_info['email'] : '',
                    'BillingAddress1'           =>    ( !empty( $retailer_info['address'] ) ) ? $retailer_info['address'] : '',
                    'BillingCompany'           =>    ( !empty( $retailer_info['company'] ) ) ? $retailer_info['company'] : '',
                    'BillingCity'              =>    ( !empty( $retailer_info['city'] ) ) ? $retailer_info['city'] : '',
                    'BillingState'             =>    ( !empty( $retailer_info['state'] ) ) ? $retailer_info['state'] : '',
                    'BillingZip'               =>    ( !empty( $retailer_info['zip'] ) ) ? $retailer_info['zip'] : '',
                    'Website'          =>    ( !empty( $retailer_info['website'] ) ) ? $retailer_info['website'] : '',
                    'CharityName'      =>    ( !empty( $product->get_name() ) ) ? $product->get_name() : '',
                    'CurrencySymbol'   => get_woocommerce_currency_symbol(),
                    'TotalAmount'      => (float)$product_price,
                    'Time'              =>  gmdate("Y-m-d H:i:s")
            );

            // Send the data to the specified endpoint
            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => json_encode($data)
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($endpoint, false, $context);

            if ($result) { //
            }
         }

    }

}
