<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

/**
 * The public-facing functionality of the plugin.
 *
 * @link  www.wisdmlabs.com
 * @since 1.0.0
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     WisdmLabs, India <support@wisdmlabs.com>
 */
class EdwiserMultipleUsersCoursePurchasePublic
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     *
     * @var string The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     *
     * @var string The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        include_once dirname(plugin_dir_path(__FILE__)) . '/public/class-edwiser-enroll-multiple-user-shortcode.php';
        add_action('init', array('\app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserEnrollMultipleUserShortcode', 'init'));

        add_action('pre_post_update', array($this, 'preventProductEditIfPurchased'));
        add_action('admin_init', array($this, 'wooProductEditWarning'));
        add_action('before_delete_post', array($this, 'productDeletePrecheck'));

        add_action('woocommerce_before_cart_table', array($this, 'showCheckboxOnCartPage'));

        //v1.1.1
        //Adding filter to display custom message to cart summary page
        add_filter('woocommerce_cart_item_name', array($this, 'showGroupedProductMessage'), 10, 2);

        add_filter('woocommerce_is_sold_individually', array($this, 'manageCartPageProductQuantityFiled'), 10, 2);
        add_action('wp_ajax_check_for_different_products', array($this, 'checkForDifferentProducts'));
        add_action('wp_ajax_nopriv_check_for_different_products', array($this, 'checkForDifferentProducts'));
        add_filter('woocommerce_billing_fields', array($this, 'addCohortFieldOnCheckout'), 10, 1);
        add_filter('woocommerce_update_cart_action_cart_updated', array($this, 'updateSingleGroupCreation'), 10, 1);
    }

    public function updateSingleGroupCreation($update)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['createDifferentGroup']) && $_SESSION['createDifferentGroup'] == 0) {
            if (!$this->checkCartItemQtyEqlForGroupCreation()) {
                $_SESSION['createDifferentGroup'] = 1;
                WC()->session->set('eb-bp-create-same-product', 0);
            }
        }
        return $update;
    }

    /**
     * Provides the functionality to disable the quantity
     * filed on the product page.if the group purchase is disabled.
     *
     * @param type $return default return value for the callback
     * @param type $product object of the woocomerce product
     * @return boolean
     */
    public function manageCartPageProductQuantityFiled($return, $product)
    {
        global $woocommerce;
        $cart_item = $woocommerce->cart->get_cart();
        $productId = $product->get_id();
        foreach ($cart_item as $item) {
            if ($item["product_id"] == $productId) {
                if (isset($item['wdm_edwiser_self_enroll']) && $item['wdm_edwiser_self_enroll'] == "on") {
                    $return = false;
                } else {
                    $return = true;
                }
            }
        }
        return $return;
    }

    /**
     * Provides the functioanlity to check if the product is purchased by one
     * or more than one user then prevent form deleting the product and display
     * the warning message to the user.
     *
     * @param integer $productId product post id which is going to delete.
     *
     * @since 1.0.1
     *
     */
    public function productDeletePrecheck($productId)
    {
        $post = get_post($productId);
        if ($post->post_type == 'product' && $this->isProductPurchased($productId)) {
            $editPostUrl = admin_url("edit.php?post_status=trash&post_type=product&eb_edit_warning=delete");
            wp_redirect($editPostUrl);
            die();
        }
    }

    public function addCohortFieldOnCheckout($fields)
    {
        $flag = 0;
        if (WC()->session->get('eb-bp-create-same-product')) {
            $flag = WC()->session->get('eb-bp-create-same-product');
        }
        if ($flag) {
            $singleGroup = 0;
            if (isset($_SESSION['createDifferentGroup'])) {
                $singleGroup = $_SESSION['createDifferentGroup'];
            }
            $new = ["cohort_name" => array("label" => "Group Name", "placeholder" => "Enter group name", "required" => 1, "different_group" => $singleGroup)];
            $keys = array_keys($fields);
            $index = array_search("billing_company", $keys);
            $pos = false === $index ? count($fields) : $index + 1;

            return array_merge(array_slice($fields, 0, $pos), $new, array_slice($fields, $pos));
        }
        return $fields;
    }

    /**
     * function to set the value of different group checkbox in session
     * @return [type] [description]
     */
    public function checkForDifferentProducts()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_POST['single_group'])) {
            if ($_POST['single_group']) {
                if ($this->checkCartItemQtyEqlForGroupCreation()) {
                    $_SESSION['createDifferentGroup'] = 0;
                    WC()->session->set('eb-bp-create-same-product', 1);
                    $msg = __("Success: Successfully enabled single group creation for group products.", 'ebbp-textdomain');
                } else {
                    wp_send_json_error(__("Error: Filed to enable single group creation, Please make the all group products quantity equal and update your cart.", 'ebbp-textdomain'));
                }
            } else {
                $_SESSION['createDifferentGroup'] = 1;
                WC()->session->set('eb-bp-create-same-product', 0);
                $msg = __("Success: Successfully disabled single group creation for group products.", 'ebbp-textdomain');
            }
            wp_send_json_success($msg);
        }
    }

    private function checkCartItemQtyEqlForGroupCreation()
    {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $quantity = array();
        foreach ($items as $item => $values) {
            $item = $item;
            if ($values['wdm_edwiser_self_enroll'] == 'on') {
                $quantity[$values['product_id']] = $values['quantity'];
            }
        }
        if (count(array_unique($quantity)) != 1) {
            return false;
        }
        return true;
    }

    /**
     * function to show checkbox on the cart page
     * @return [type] [description]
     */
    public function showCheckboxOnCartPage()
    {
        global $woocommerce;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $items = $woocommerce->cart->get_cart();
        $flag = 0;
        foreach ($items as $item => $values) {
            $item = $item;
            $product_options = get_post_meta($values['product_id'], 'product_options', true);
            if (isset($product_options['moodle_course_group_purchase']) && "on" == $product_options['moodle_course_group_purchase'] && isset($values['wdm_edwiser_self_enroll']) && $values['wdm_edwiser_self_enroll'] == "on") {
                $flag += 1;
            }
        }
        
        if ($flag > 1) {
            $checked = '';
            if (WC()->session->get('eb-bp-create-same-product')) {
                $checked = 'checked';
            }
            ?>
            <div>
                <div class="wdm-diff-prod-qty-error wdm-hide">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <span id="wdm-diff-prod-qty-error-msg"></span>
                </div>
                <div class="wdm-diff-prod-qty-success wdm-hide">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    <span id="wdm-diff-prod-qty-success-msg"></span>
                </div>
                <div class="wdm-cartp-group-chk-box">
                    <input id="mucp-cart-group-checkbox" type="checkbox" name="mucp-group-checkbox" title="<?php _e("This will allow to create the same product for all the courses products", 'ebbp-textdomain'); ?>" <?php echo $checked; ?>>
                    <label><?php _e("Add all product in same group", 'ebbp-textdomain'); ?></label>
                </div>
            </div>
            <?php
        }

        $_SESSION["cart-bulk-product-count"] = $flag;
    }

    /**
     * Provides the functioanlity to display that product is grouped
     * product is group purchase is enabled in single cart page
     * @param $product_get_title, $cart_item, $cart_item_key
     * @return string title to be displayed
     */
    public function showGroupedProductMessage($product_get_title, $cart_item)
    {
        $title = "Group Purchase Enabled";
        if (isset($cart_item['wdm_edwiser_self_enroll']) && $cart_item['wdm_edwiser_self_enroll'] != 'no') {
            return sprintf('%s <div><span class = "wdm-bulk-purchase-message">%s</span></div>', $product_get_title, $title);
        } else {
            return $product_get_title;
        }
    }

    /**
     * Provides the functioanlity to display the admin notice if the eb course
     * related product is get deleted or edit and if the course has associated
     * remaining qunatity one or more than one.
     *
     * @since 1.0.1
     *
     */
    public function wooProductEditWarning()
    {
        if (isset($_GET['eb_edit_warning'])) {
            $edit = $_GET['eb_edit_warning'] == "edit" ? __("change associated courses.", 'ebbp-textdomain') : __("delete the product permanently.", 'ebbp-textdomain');
            ?>
            <div id="eb_edit_warning" class="notice notice-warning is-dismissible">
                <p>
                    <?php
                    printf(
                        __('This product is purchased by more than one user can\'t %s', 'ebbp-textdomain'),
                        $edit
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Provides the functioanlity to check if the product is purchased by one or
     * more than one user and if the product is purchased by one or more than
     * one user then prevent post update and redirect user to product edit page.
     *
     * @param Integer $post post id which is goin to edit.
     *
     * @since 1.0.1
     *
     */
    public function preventProductEditIfPurchased($post)
    {

        if (isset($_POST['action']) && $_POST['action'] == 'editpost') {
            if (isset($_POST['post_ID'])) {
                $postId = $_POST['post_ID'];
                if ($_POST['post_type'] == 'product') {
                    if ($this->isProductPurchased($postId)) {
                        $oldProdCourse = get_post_meta($postId, 'product_options', true);
                        $oldProdCourse = isset($oldProdCourse['moodle_post_course_id']) ? $oldProdCourse['moodle_post_course_id'] : false;
                        $newProdCourse = isset($_POST['product_options']['moodle_post_course_id']) ? $_POST['product_options']['moodle_post_course_id'] : false;
                        if ($oldProdCourse != $newProdCourse) {
                            $editPostUrl = admin_url("post.php?post=$postId&action=edit&eb_edit_warning=edit");
                            //Prevent changing the database records and return to eddit page
                            wp_redirect($editPostUrl);
                            die();
                        }
                    }
                }
            }
        }
        unset($post);
    }

    /**
     * Provides the functioanlity to check if the product is purchased by one
     * or more than one user.
     * @param Integer $productId the product id to check is the number of
     * availabler sites are not less than one.
     * @return boolean true if the product avaiulable sites quantity is not
     * less than zero. otherwise returns false.
     *
     * @since 1.0.1
     *
     */
    private function isProductPurchased($productId)
    {
        global $wpdb;
        $query = "SELECT meta_value FROM  $wpdb->usermeta WHERE  `meta_key`='group_products' ";
        $result = $wpdb->get_results($query);
        $products = array();
        foreach ($result as $value) {
            $courses = @unserialize($value->meta_value);
            $products = $this->productQuantity($products, $courses);
        }
        if (array_key_exists($productId, $products) && $products[$productId] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Provides the functionality to add the product quantity to check is the
     * product quantity is more than zero.
     *
     * @since 1.0.1
     *
     */
    private function productQuantity($oldProduct, $newProduct)
    {
        foreach ($newProduct as $key => $value) {
            if (array_key_exists($key, $oldProduct)) {
                $oldProduct[$key]+=$newProduct[$key];
            } else {
                $oldProduct[$key] = $newProduct[$key];
            }
            $value = $value;
        }
        return $oldProduct;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueueStyles()
    {

        /**
         * Performance issue - Loaded css on shortcode page and on single product page. Edit condition if you want load in other cases.
         * @author Pandurang
         * @since 1.0.1
         */
        /**
         * data tables libarary for enroll users
         * @author krunal
         * @since 1.1.0
         */
        wp_enqueue_style('jquery-min-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css');
        wp_enqueue_style('jquery_dataTables_min', plugin_dir_url(__FILE__) . 'css/jquery_dataTables_min.css');
        wp_enqueue_style('responsive_dataTables_min', plugin_dir_url(__FILE__) . 'css/responsive_dataTables_min.css');
        wp_enqueue_style('buttons_dataTables_min', plugin_dir_url(__FILE__) . 'css/buttons_dataTables_min.css');
        wp_enqueue_style('select_dataTables_min', plugin_dir_url(__FILE__) . 'css/select_dataTables_min.css');
        wp_enqueue_style($this->plugin_name . '_font_awesome', plugin_dir_url(__FILE__) . 'css/font-awesome-4.4.0/css/font-awesome.min.css', array(), '1.0.2', 'all');

        global $post;
        if ($post != null && ( property_exists($post, 'post_content') && has_shortcode($post->post_content, 'bridge_woo_enroll_users') ) || is_singular('product')) {
            wp_enqueue_style('wdm_bootstrap_css', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');

            wp_enqueue_style('bootstrap_file_input_min_css', plugin_dir_url(__FILE__) . 'css/fileinput.min.css', array(), '1.0.2', 'all');

            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/edwiser-multiple-users-course-purchase-public.css', array(), '1.0.2', 'all');

            wp_enqueue_style('wdm_front_end_css', plugin_dir_url(__FILE__) . 'css/edwiser-frontend-style.css');
        }

        wp_enqueue_style('wdm_front_end_css', plugin_dir_url(__FILE__) . 'css/edwiser-frontend-style.css');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueueScripts()
    {

        /**
         * Performance issue - Loaded js on shortcode page and on single product page. Edit condition if you want load in other cases.
         * @author Pandurang
         * @since 1.0.1
         */
        /**
         * data tables libarary for enroll users
         * @author krunal
         * @since 1.1.0
         */
        wp_enqueue_script('jquery_dataTables_min', plugin_dir_url(__FILE__) . 'js/jquery_dataTables_min.js', array(), '3.3.4', true);
        wp_enqueue_script('dataTables_responsive_min', plugin_dir_url(__FILE__) . 'js/dataTables_responsive_min.js', array(), '3.3.4', true);
        /*        wp_enqueue_script(
          'dataTables_buttons_min',
          plugin_dir_url(__FILE__) . 'js/dataTables_buttons_min.js',
          array(),
          '3.3.4',
          true
          ); */

        wp_enqueue_script('dataTables_select_min', plugin_dir_url(__FILE__) . 'js/dataTables_select_min.js', array(), '3.3.4', true);

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/edwiser-multiple-users-course-purchase-public.js', array('jquery'), $this->version, false);

        wp_localize_script($this->plugin_name, 'ebbpPublic', array(
            'addNewUser' => __('Add New User', 'ebbp-textdomain'),
            'enroll' => __('Enroll', 'ebbp-textdomain'),
            'enterFirstName' => __('Enter First Name : * ', 'ebbp-textdomain'),
            'enterLastName' => __('Enter Last name : * ', 'ebbp-textdomain'),
            'enterEmailName' => __('Enter E-mail ID : * ', 'ebbp-textdomain'),
            'mandatoryMsg' => __('All fields marked with * are mandatory.', 'ebbp-textdomain'),
            'slctValidFile' => __('Please select a valid CSV file. Required headers are <b>First Name</b>, <b>Last Name</b>, <b>Username</b> and <b>Email</b>.', 'ebbp-textdomain'),
            'invalidEmailId' => __('Invalid Email ID:', 'ebbp-textdomain'),
            'user' => __('user', 'ebbp-textdomain'),
            'youCanEnrollOnly' => __('You can enroll only', 'ebbp-textdomain'),
            'uploadFileFirst' => __('Please upload CSV file first.', 'ebbp-textdomain'),
            'wdm_user_import_file' => plugins_url('/edwiser-multiple-users-course-purchase-upload-csv.php', __FILE__),
            'ajax_url' => admin_url() . '/admin-ajax.php',
            'remove_url' => plugin_dir_url(__FILE__) . 'images/Remove-icon.png',
            /**
             * enroll user diaplay string
             * @since 1.1.0
             */
            'emptyTable' => __('Sorry, No users Enrolled Yet', 'ebbp-textdomain'),
            'enterQuantity' => __('Please enter quantity', 'ebbp-textdomain'),
            'associatedCourse' => __('Associated Courses', 'ebbp-textdomain'),
            'enrollUser' => __('Enroll User', 'ebbp-textdomain'),
            'enrollNewUser' => __('Enroll New User', 'ebbp-textdomain'),
            'cancel' => __('Cancel', 'ebbp-textdomain'),
            'proctocheckout' => __('Proceed to checkout', 'ebbp-textdomain'),
            'ok' => __('OK', 'ebbp-textdomain'),
            'addQuantity' => __('Add Quantity', 'ebbp-textdomain'),
            'addNewProductsIn' => __('Add new products in ', 'ebbp-textdomain'),
            'saveChanges' => __('Save Changes', 'ebbp-textdomain'),
            'close' => __('Close', 'ebbp-textdomain'),
            'insufficientQty' => __('Insufficient Quantity. Please Add more quantity', 'ebbp-textdomain')
                ));

        global $post;

        if ($post != null && (property_exists($post, 'post_content') && has_shortcode($post->post_content, 'bridge_woo_enroll_users') ) || is_singular('product')) {
            wp_enqueue_script('jquery');


            wp_enqueue_script('bootstrap_min_js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array(), '3.3.4', true);

            // for jquery ui
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');

            wp_enqueue_script('bootstrap_canvas_js', plugin_dir_url(__FILE__) . 'js/plugins/canvas-to-blob.min.js', array('jquery'), $this->version, false);

            wp_enqueue_script('bootstrap_fileinput_min_js', plugin_dir_url(__FILE__) . 'js/fileinput.min.js', array('jquery'), $this->version, false);

            wp_enqueue_script($this->plugin_name);
        }
    }

    public function wdmWoocommerceQuantityInputMax($qty, $product)
    {
        $post_meta = get_post_meta($product->id, 'product_options', true);
        if (isset($post_meta['moodle_post_course_id'])) {
            if ('no' == $post_meta['moodle_course_group_purchase']) {
                return 1;
            }
        }
        return $qty;
    }
    /*
     * Check Add to cart validation for quantity of multiple user course enroll
     */

    public function wdmMuMaxItemQuantityValidation($value, $product_id, $quantity)
    {
        $post_meta = get_post_meta($product_id, 'product_options', true);
        if (isset($post_meta['moodle_post_course_id'])) {
            if ('no' == $post_meta['moodle_course_group_purchase']) {
                if ($quantity > 1) {
                    $value = 'false';
                }
                return $this->checkProductInCart($product_id);
            }
        }
        return $value;
    }

    public function checkProductInCart($product_id)
    {
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $val) {
            $_product = $val['data'];
            if ($product_id == $_product->id) {
                update_post_meta($_product->id, '_sold_individually', 'yes');

                return false;
            }
        }
        return true;
    }
}
