<?php
/**
 * 
 * @global type $post
 * @global WC_Payment_Gateways $woo
 * @return type
 */
function wpp_payments_form() {
    global $post, $woo;
    $html ='';
    $productIds = get_option('woocommerce_product_apply', array());
    if (is_array($productIds)) {
        foreach ($productIds as $key => $product) {
            if (!get_post($product) || !count(get_post_meta($product, 'sd_payments', true))) {
                unset($productIds[$key]);
            }
        }
    }
    update_option('woocommerce_product_apply', $productIds);
    $postPayments = get_post_meta($post->ID, 'sd_payments', true) ? get_post_meta($post->ID, 'sd_payments', true) : array();

    $woo = new WC_Payment_Gateways();
    $payments = $woo->payment_gateways;
    foreach ($payments as $pay) {
        /**
         *  skip if payment in disbled from admin
         */
        if ($pay->enabled == 'no') {
            continue;
        }
        $checked = '';
        if (is_array($postPayments) && in_array($pay->id, $postPayments)) {
            $checked = ' checked="yes" ';
        }
        $html .=' 
        <input type="checkbox" '.$checked.' value="'.$pay->id.'" name="pays[]" id="payment_'.$pay->id.' />
        <label for="payment_'.$pay->id.'">'.$pay->title.'</label> 
        <br /> ';
    }
    echo $html;
}

/**
 * 
 * @param type $post_id
 * @param type $post
 * @return type
 */
function wpp_meta_box_save($post_id, $post) {
    // Restrict to save for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // Restrict to save for revisions
    if (isset($post->post_type) && $post->post_type == 'revision') {
        return $post_id;
    }
    if ( isset($_REQUEST['action']) &&  $_REQUEST['action'] != 'editpost') {
        return $post_id;
    }else{
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'product' && isset($_POST['pays'])) {
            $productIds = get_option('woocommerce_product_apply', array());
            if (is_array($productIds) && !in_array($post_id, $productIds)) {
                $productIds[] = $post_id;
                update_option('woocommerce_product_apply', $productIds);
            }
                //delete_post_meta($post_id, 'sd_payments');    
            $payments = array();
            if ($_POST['pays']) {
                foreach ($_POST['pays'] as $pay) {
                    $payments[] = $pay;
                }
            }
                update_post_meta($post_id, 'sd_payments', $payments);
        } elseif (isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
                update_post_meta($post_id, 'sd_payments', array());
        }
    }
}

/**
 * 
 * @global type $woocommerce
 * @param type $available_gateways
 * @return type
 */
function wpppayment_gateway_disable_country($available_gateways) {
    global $woocommerce;
    $arrayKeys = array_keys($available_gateways);
    /**
     * default setting
     */
    $softsdev_wpp_plugin_settings = get_option('sdwpp_plugin_settings', array('default_payment' => ''));
    $default_payment = $softsdev_wpp_plugin_settings['default_payment'];
    $is_default_pay_needed = false;
    /**
     * checking all cart products
     */
    if (is_object($woocommerce->cart)) {
        $items = $woocommerce->cart->cart_contents;
        $itemsPays = '';
        if (is_array($items)) {
            foreach ($items as $item) {

                if (!is_product_eligible($item['product_id'])) {
                    continue;
                }

                $itemsPays = get_post_meta($item['product_id'], 'sd_payments', true);

                if (is_array($itemsPays) && count($itemsPays)) {
                    foreach ($arrayKeys as $key) {
                        if (array_key_exists($key, $available_gateways) && !in_array($available_gateways[$key]->id, $itemsPays)) {
                            if ($default_payment == $key) {
                                $is_default_pay_needed = true;
                                $default_payment_obj = $available_gateways[$key];
                                unset($available_gateways[$key]);
                            } else {
                                unset($available_gateways[$key]);
                            }
                        }
                    }
                }
            }
        }
        /**
         * set default payment if there is none
         */
        if ($is_default_pay_needed && count($available_gateways) == 0) {
            $available_gateways[$default_payment] = $default_payment_obj;
        }
    }
    return $available_gateways;
}

/**
 * 
 */
function wpp_meta_box_add() {
    global $post;
    if (isset($post->ID) && is_product_eligible($post->ID)) {
        add_meta_box('payments', 'Payments', 'wpp_payments_form', 'product', 'side', 'core');
    }
}


/**
 * 
 * @param type $product_id
 * @return boolean
 */
function is_product_eligible($product_id) {
    # Product object
    $product_object = wc_get_product($product_id);
    if( !$product_object || $product_object->post_type != 'product'){
            return false;	
    }		
    $softsdev_selected_cats = get_option('softsdev_selected_cats');

	$softsdev_selected_cats = explode(',', $softsdev_selected_cats);
    if ($softsdev_selected_cats) {
        $is_eligible = false;
        # Get visiblity
        $current_visibility = $product_object->get_catalog_visibility();
        # Get Category Ids
        $cat_ids = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        # Convert saved array in to list
        $softsdev_selected_cats = is_array($softsdev_selected_cats) ? $softsdev_selected_cats : array($softsdev_selected_cats);
        foreach ($cat_ids as $cat_id) {
            if (in_array($cat_id, $softsdev_selected_cats)) {
                $is_eligible = true;
                break;
            }
        }
        # check visiblity in array or now define
        if ($is_eligible && in_array($current_visibility, array('catalog', 'visible'))) {
            $is_eligible = true;
        } else {
            $is_eligible = false;
        }
        # return eligiblity
        return $is_eligible;
    }
    return false;
}
