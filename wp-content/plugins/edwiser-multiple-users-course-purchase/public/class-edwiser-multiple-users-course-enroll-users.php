<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

use app\wisdmlabs\edwiserBridge as edwiserBridge;

/**
 * The public-facing functionality of enroll user.
 * @author     WisdmLabs, India <support@wisdmlabs.com>
 */
class EdwiserMultipleCourseEnrollUsers
{

    public function __construct()
    {
        add_action('wp_ajax_get_user_bulk_course_details', array($this, 'wdmEnrolledUsers'));
        add_action('wp_ajax_get_enrol_user_details', array($this, 'wdmEnrollUserDetails'));
        add_action('wp_ajax_get_enrol_user_course', array($this, 'wdmEnrollUserCourse'));
        add_action('wp_ajax_edit_user', array($this, "wdmEditUser"));
        add_action('wp_ajax_ebbp_add_to_cart', array($this, "wdmAddToCart"));
        add_action('wp_ajax_ebbp_add_quantity', array($this, 'wdmAddMoreQuantity'));
        add_action('wp_ajax_ebbp_add_new_product', array($this, 'wdmAddNewProductToGroup'));
    }

    public function wdmEnrolledUsers()
    {
        if (isset($_POST['mdl_cohort_id']) && !empty($_POST['mdl_cohort_id'])) {
            $mdlCohortId = $_POST['mdl_cohort_id'];
            $cuser_id = get_current_user_id();
            $avail_seats = 0;
            global $wpdb;
            //v1.1.1
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT PRODUCTS FROM $tableName WHERE mdl_cohort_id = %d", $mdlCohortId);
            $result = $wpdb->get_var($query);
            $products = unserialize($result);
            $avail_seats = min($products);
            if ($avail_seats==null) {
                $avail_seats=0;
            }
            $tbl_name = $wpdb->prefix . 'moodle_enrollment';

            /**
             * Fixed #32173 - Backward compatibility v1.0.0
             * @author Pandurang
             * @since 1.0.1
             */
            $query = $wpdb->prepare(
                "SELECT DISTINCT `user_id` FROM `{$tbl_name}` WHERE `enrolled_by` = %d AND `mdl_cohort_id` = '%d'",
                $cuser_id,
                $mdlCohortId
            );

            $enrolled_users = $wpdb->get_results($query);
            ob_start();
            ?>
            <label>
                <?php
                _e('Enrolled Users:', 'ebbp-textdomain');
                echo "  ".count($enrolled_users);
                ?>
            </label>
            <table id='enroll-user-table'>
                <thead>
                    <tr>
                        <th><?php _e("ID", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("FirstName", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("LastName", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("Email Id", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("Role", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("Manage", 'ebbp-textdomain'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($enrolled_users)) {
                        foreach ($enrolled_users as $userId) {
                            $user_data = get_userdata($userId->user_id);
                            $userRole = $this->getUserRole($user_data);
                            ?>
                            <tr>
                                <td><?php echo $userId->user_id; ?></td>
                                <td><?php echo $user_data->first_name; ?></td>
                                <td><?php echo $user_data->last_name; ?></td>
                                <td><?php echo $user_data->user_email; ?></td>
                                <td><?php echo $userRole; ?></td>
                                <td>
                                    <button id="<?php echo $userId->user_id; ?>" class='edit-enrolled-user'><?php _e("Edit", 'ebbp-textdomain'); ?></button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                        ?>
                    </tbody>
                </table>
                <?php
                    $form = ob_get_clean();
                    $responce = array('seats' => $avail_seats, 'html' => $form);
                    wp_send_json_success($responce);
        } else {
            wp_send_json_error(__("Invalid request data"));
        }
    }

    /**
     * function to show all users
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    public function getUserRole($user)
    {
        $userRole = $user->roles;
        $str = "";
        $count = count($userRole);
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                if ($userRole[$i] == 'non_editing_teacher') {
                    $str .= "Non Editing Teacher";
                } else {
                    $str .= $userRole[$i];
                }
            } else {
                if ($userRole[$i] == 'non_editing_teacher') {
                    $str .= ", Non Editing Teacher";
                } else {
                    $str .= ", " . $userRole[$i];
                }
            }
        }
        return $str;
    }

    public function wdmEnrollUserDetails()
    {
        $uid = trim($_POST["uid"]);
        $user_data = get_userdata($uid);
        $first_name = $user_data->first_name;
        $last_name = $user_data->last_name;
        $Email = $user_data->user_email;
        $roles = $user_data->roles;

        foreach ($roles as $role) {
            if ($role = "subscriber") {
                $userRole = "student";
                break;
            } else {
                $userRole = "Manager";
            }
        }

        echo json_encode(array("FirstName" => $first_name, "lastname" => $last_name, "email" => $Email, "role" => $userRole));
        die();
    }

    public function wdmEnrollUserCourse()
    {
        $mdlCOhortId = $_POST['mdl_cohort_id'];
        if (isset($mdlCOhortId) && !empty($mdlCOhortId)) {
            global $wpdb;
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT PRODUCTS,COHORT_NAME FROM $tableName WHERE MDL_COHORT_ID = %d;", $mdlCOhortId);
            $result = $wpdb->get_row($query, ARRAY_A);
            $product = $result['PRODUCTS'];
            $cohortName = $result['COHORT_NAME'];
            $product = unserialize($product);
            ob_start();
            ?>
            <div class="wdm-coho-asso-corses wdm-dialog-scroll">
                <ol>
                    <?php
                    foreach ($product as $key => $value) {
                        $value = $value;
                        $tbl_name = $wpdb->prefix . "woo_moodle_course";
                        $query = $wpdb->prepare(
                            "SELECT DISTINCT `moodle_post_id` FROM `{$tbl_name}` WHERE `product_id` = %d ",
                            $key
                        );
                        $courses = $wpdb->get_col($query);
                        $productName = get_the_title($key);
                        ?>
                        <li>
                            <strong> <?php echo $productName; ?></strong>
                            <ul>
                                <?php
                                foreach ($courses as $course) {
                                    $courseInfo = get_post($course);
                                    $title = $courseInfo->post_title;
                                    ?>
                                    <li><?php echo $title; ?></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>
                </ol>
            </div>
            <?php
            $responce = ob_get_clean();
            $current_user=  wp_get_current_user();
            
            $cohortName=  str_replace($current_user->user_login."_", "", html_entity_decode($cohortName));
            wp_send_json_success(array('html' => $responce, 'cohortName' => $cohortName));
        } else {
            wp_send_json_error(__("Invalid request parameters", 'ebbp-textdomain'));
        }
    }

    /**
     * Callback to update the user on wordpress and moodle.
     */
    public function wdmEditUser()
    {
        $uid = $this->checkData($_POST, "uid");
        $fnane = $this->checkData($_POST, "firstname");
        $lname = $this->checkData($_POST, "lastname");
        $email = $this->checkData($_POST, "email");
        if ($uid && $fnane && $lname && $email) {
            $userData = array(
                "ID" => $uid,
                "first_name" => $fnane,
                "last_name" => $lname,
                "user_email" => $email,
            );

            /**
             * Update user on wordpress.
             */
            $updateUser = wp_update_user($userData);
            if (is_wp_error($updateUser)) {
                wp_send_json_error($this->prepareResMsg($updateUser->get_error_message(), true));
            } else {
                $moodleUserId = get_user_meta(trim($_POST["uid"]), 'moodle_user_id', true);
                $user_data = array(
                    "id" => $moodleUserId,
                    "firstname" => $fnane,
                    "lastname" => $lname,
                    "email" => $email
                );
                /**
                 * Update user data on moodle.
                 */
                $mdlUserUpdated = edwiserBridge\edwiserBridgeInstance()->userManager()->createMoodleUser($user_data, 1);
                if ($mdlUserUpdated['user_updated'] == 1) {
                    wp_send_json_success($this->prepareResMsg(__("User data has been updated successfully.", 'ebbp-textdomain'), false));
                } else {
                    wp_send_json_error($this->prepareResMsg(__("Failed to update user on moodle.", 'ebbp-textdomain'), true));
                }
            }
        } else {
            wp_send_json_error($this->prepareResMsg(__("User data is inappropriate.", 'ebbp-textdomain'), true));
        }
    }

    private function prepareResMsg($msg, $isError = false)
    {
        ob_start();
        if (!$isError) {
            ?>
            <div class="wdm_success_message wdm_user_list">
                <i class="fa fa-times-circle wdm_success_msg_dismiss"></i>
                <span class="wdm_enroll_warning_message_lable">
                    <?php echo $msg; ?>
                </span>
            </div>
            <?php
        } else {
            ?>
            <div class="wdm_error_message wdm_user_list">
                <i class="fa fa-times-circle wdm_error_msg_dismiss"></i>
                <span class="wdm_enroll_warning_message_lable">
                    <?php echo $msg; ?>
                </span>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    private function checkData($array, $key)
    {
        if (isset($array[$key]) && !empty($array[$key])) {
            return $array[$key];
        }
        return false;
    }

    public function wdmAddToCart()
    {
        $session_data = array();
        if (WC()->session->get('add_product_from_enroll_page')) {
            $session_data = WC()->session->get('add_product_from_enroll_page');
        }

        if (isset($_REQUEST["mdl_cohort_id"]) && !empty($_REQUEST["mdl_cohort_id"]) && isset($_REQUEST['productQuantity']) && !empty($_REQUEST['productQuantity'])) {
            global $woocommerce;
            $checkoutUrl = "#";
            $cohortId=$_REQUEST['mdl_cohort_id'];

            $cohortDetails = array("cohort_id" => $_REQUEST['mdl_cohort_id']);

            // $session_data['cohortId'] = $_REQUEST['mdl_cohort_id'];
            $flag = 0;
            foreach ($_REQUEST['productQuantity'] as $value) {
                if ($value <= 0) {
                    $flag = 1;
                }
            }
/*            $wcSession = [];
            $wcSession ["cohortId"] = $cohortId;*/
            if (!$flag) {
                foreach ($_REQUEST['productQuantity'] as $prodId => $qty) {
                    $cohortDetails["product_id"] = $prodId;
                    $cohortDetails["quantity"] = $qty;
                    // $session_data[$prodId] = $qty;
                    $woocommerce->cart->add_to_cart($prodId, $qty, "", array(), array('cohort_id'=>$cohortId,'wdm_edwiser_self_enroll'=>'on','Group Enrollment'=> 'yes'));
                }

                array_push($session_data, $cohortDetails);
                // Setting the order details in session
                WC()->session->set('add_product_from_enroll_page', $session_data);
                WC()->session->set('add_product_from_enroll_page', $session_data);
                $checkoutUrl = $woocommerce->cart->get_checkout_url();
            }
        }
        if (empty($checkoutUrl)) {
            wp_send_json_error(__("Checkout page not found, Please contact admin", 'ebbp-textdomain'));
        }
        wp_send_json_success($checkoutUrl);
    }

    public function wdmAddMoreQuantity()
    {
        // wp_send_json_error("Error occured while adding cou??rses");
        if (WC()->session->get('eb-bp-create-same-product')) {
            WC()->session->set('eb-bp-create-same-product', 0);
        }
        //v1.1.1
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["addQuantity"] = 1;
        $currency = get_woocommerce_currency_symbol();
        $cohortId = $_POST['mdl_cohort_id'];
        global $wpdb;
        $tableName = $wpdb->prefix . "bp_cohort_info";
        $query = $wpdb->prepare("SELECT PRODUCTS FROM $tableName WHERE MDL_COHORT_ID = %d", $cohortId);
        $result = $wpdb->get_var($query);
        $product = @unserialize($result);
        ob_start();
        ?>
        <div id='add-quantity'>
            <div class="wdm-add-prod_qty">
                <div style="display: table-row;">
                    <label for="wdm_new_prod_qty" style="display: table-cell;padding-right:10px;">Enter Quantity:</label>               
                    <input type="number" min="0" name="wdm_new_prod_qty" value="0" id="wdm_new_prod_qty" style="display: table-cell;">
                </div>
            </div>
            <table id ='add-quantity-table' class="wdm-more-qty-tbl" border="0" data-cohortid='<?php echo $cohortId; ?>'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php _e("Product Name", 'ebbp-textdomain'); ?></th>                        
                        <th><?php _e("Price", 'ebbp-textdomain'); ?></th>
                        <th></th>
                        <th><?php _e("Quantity", 'ebbp-textdomain'); ?></th>
                        <th></th>
                        <th><?php _e("Price", 'ebbp-textdomain'); ?></th>
                    </tr>
                </thead>
                <tbody class="wdm-dialog-scroll">
                    <?php
                    $cnt = 0;
                    foreach ($product as $pid => $quantity) {
                        $cnt++;
                        $quantity = $quantity;
                        $courses = $this->getCoursesAssociatedWithProduct($pid);
                        ?>       
                        <tr>
                            <td><?php echo $cnt . "."; ?></td>
                            <td class='wdmProductNameContainer'>
                                <ul class='wdmProductName'>
                                    <li class = 'product_title'><?php echo get_the_title($pid); ?><ul>
                                            <?php
                                            foreach ($courses as $course) {
                                                ?>
                                                <li> - <?php echo get_the_title($course); ?></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                            <?php
                            //v1.1.1
                            $productPrice = $this->getProductPrice($pid);
                            ?>
                            <td>
                                <span><?php echo $currency; ?></span>
                                <span id = '<?php echo $pid; ?>-per-product-price'><?php echo $productPrice; ?></span>
                            </td>
                            <td> x </td>
                            <td>
                                <span class="wdm_new_qty_per_prod" id="<?php echo $pid; ?>">0</span></td>
                            </td>
                            <td> = </td>
                            <td>
                                <div class="wdm-item-price">
                                    <span><?php echo $currency; ?></span>
                                    <span class = 'wdm-quantity-total add-more-quantity' id='<?php echo $pid; ?>-total-price'>0</span>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td >
                            <span><?php echo $currency; ?></span>
                            <span id='add-quantity-total-price'>0</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php
        if (count($product) <= 0) {
            wp_send_json_error(__("'Sorry, currently there are no group products available '", 'ebbp-textdomain'));
        }
        $responce = ob_get_clean();
        wp_send_json_success($responce);
    }

    public function wdmAddNewProductToGroup()
    {
        if (WC()->session->get('eb-bp-create-same-product')) {
            WC()->session->set('eb-bp-create-same-product', 0);
        }
        $currency = get_woocommerce_currency_symbol();
        $mdlCohortId = $_POST['mdl_cohort_id'];
        global $wpdb;
        $tblCohoInfo = $wpdb->prefix . "bp_cohort_info";
        $stmtSelecCohoInfo = $wpdb->prepare("SELECT PRODUCTS,COHORT_NAME FROM $tblCohoInfo WHERE MDL_COHORT_ID = %d", $mdlCohortId);
        $result = $wpdb->get_row($stmtSelecCohoInfo, ARRAY_A);
        $cohortProducts = unserialize($result['PRODUCTS']);
        $avaQty = max($cohortProducts);
        $cohortName = $result['COHORT_NAME'];
        $cntCohortMemb = $this->getTotalMembers($mdlCohortId);
        $minProductQuantity = $avaQty + $cntCohortMemb;
        $tblMoodleEnroll = $wpdb->prefix . "woo_moodle_course";
        $stmtSeleMdlRec = "SELECT DISTINCT `product_id` FROM `{$tblMoodleEnroll}`";
        $allProduct = $wpdb->get_col($stmtSeleMdlRec);
        ob_start();
        ?>
        <div id ='add-quantity'>
            <table id ='add-quantity-table' class="wdm-more-prod-tbl wdm-dialog-scroll" data-cohortid='<?php echo $mdlCohortId; ?>'>
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e("Product Name", 'ebbp-textdomain'); ?></th>
                        <th><?php _e("Price", 'ebbp-textdomain'); ?></th>
                        <th></th>
                        <th><?php _e("Quantity", 'ebbp-textdomain'); ?></th>
                        <th></th>
                        <th><?php _e("Total", 'ebbp-textdomain'); ?></th>
                    </tr>
                </thead>
                <tbody class="wdm-dialog-scroll">
                    <?php
                    foreach ($allProduct as $product) {
                        $postMeta = get_post_meta($product, "product_options");
                        // v1.1.1
                        //$postMeta = unserialize($postmeta[0]);
                        if (isset($postMeta[0]['moodle_course_group_purchase']) && $postMeta[0]['moodle_course_group_purchase'] == 'on') {
                            if (array_key_exists($product, $cohortProducts)) {
                                continue;
                            } else {
                                ?>
                                <tr>
                                    <td class = 'box'>
                                        <input class='wdm_selected_products' id="<?php echo $product; ?>-wdm-sele-prod" type = 'checkbox' />
                                    </td>
                                    <td class='wdmProductNameContainer'>
                                        <ul class='wdmProductName'>
                                            <li class = 'product_title' data-id = "<?php echo $product; ?>">
                                                    <?php echo get_the_title($product); ?>
                                                <ul>
                                                    <?php
                                                    $courses = $this->getCoursesAssociatedWithProduct($product);
                                                    foreach ($courses as $course) {
                                                        ?>
                                                        <li> - <?php echo get_the_title($course); ?></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    <?php
                                    $productPrice = $this->getProductPrice($product);
                                    ?>
                                    <td>
                                        <span><?php echo $currency ?></span>
                                        <span id="<?php echo $product; ?>-per-product-price"><?php echo $productPrice; ?></span>
                                    </td>
                                    <td> x </td>
                                    <td style = 'text-align:center;'>
                                        <span class="wdm_new_qty_per_new_prod" id="<?php echo $product; ?>"><?php echo $minProductQuantity; ?></span></td>
                                    </td>
                                    <td> = </td>
                                    <td>
                                        <span><?php echo $currency; ?> </span>
                                        <span class = 'wdm-quantity-total add-more-product' id='<?php echo $product; ?>-total-price'>0</span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }

                    if (count($allProduct) <= 0) {
                        wp_send_json_error(__("'Sorry, currently there are no group products available '", 'ebbp-textdomain'));
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <span><?php echo $currency; ?></span>
                            <span id='add-quantity-total-price'>0</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
        $current_user=  wp_get_current_user();
        $cohortName=  str_replace($current_user->user_login."_", "", $cohortName);
        $responce = array("data" => ob_get_clean(), "cohort" => $cohortName);
        wp_send_json_success($responce);
    }
    
    public function getProductPrice($productId)
    {
        $product = wc_get_product($productId);
        return $product->get_price();
    }

    public function getTotalMembers($mdlCohortId)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . "moodle_enrollment";
        $query = $wpdb->prepare("SELECT DISTINCT  user_id FROM $tableName WHERE mdl_cohort_id = %d", $mdlCohortId);
        $result = $wpdb->get_results($query, ARRAY_A);
        return count($result);
    }

    public function getCoursesAssociatedWithProduct($productId)
    {
        global $wpdb;
        $tbl_name = $wpdb->prefix . "woo_moodle_course";
        $query = $wpdb->prepare("SELECT DISTINCT `moodle_post_id` FROM `{$tbl_name}` WHERE `product_id` = %d ", $productId);
        $courses = $wpdb->get_col($query);
        return $courses;
    }
}
