<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

use app\wisdmlabs\edwiserBridge as edwiserBridge;

defined('ABSPATH') || exit;
if (!class_exists('EdwiserSelfEnroll')) {

    class EdwiserSelfEnroll
    {
        protected $bulkProductNames = array();

        public function __construct()
        {
            add_filter('eb_emailtmpl_content', array($this, 'ebEmailtmplContent'), 111);

            add_action('woocommerce_before_add_to_cart_button', array($this, 'wdmLdWoocommerceBeforeAddToCartButton'), 10);
            //Store the custom field
            add_filter('woocommerce_add_cart_item_data', array($this, 'wdmLdAddCartItemCustomDataSave'), 10, 2);
            add_filter('woocommerce_get_cart_item_from_session', array($this, 'getCartItemsFromSession'), 1, 3);
            add_action('woocommerce_add_order_item_meta', array($this, 'wdmAddValuesToOrderItemMeta'), 1, 2);
            add_action('woocommerce_order_status_completed', array($this, 'wdmSaveProductQty'), 99, 1);
            add_filter('woocommerce_order_items_meta_display', array($this, 'translateEnrolledSelf'), 11, 2);
        }

        public function translateEnrolledSelf($output, $obj)
        {
            //Unused variable
            unset($obj);
            return str_replace(
                array('Group Enrollment', 'yes'),
                array(__('Group Enrollment', 'ebbp-textdomain'),
                __('yes', 'ebbp-textdomain')),
                $output
            );
        }
        /*
         * Adding 'Group Registration' item meta if group_registration enabled by user
         * @param $item_id cart item
         * @param $values list of item meta
         */
        public function wdmAddValuesToOrderItemMeta($item_id, $values)
        {
            if (isset($values['wdm_edwiser_self_enroll']) && $values['wdm_edwiser_self_enroll'] != 'no') {
                wc_add_order_item_meta($item_id, 'Group Enrollment', 'yes');
            } else {
                wc_add_order_item_meta($item_id, 'Group Enrollment', 'no');
            }
        }
        /*
         * Checking if group registration enabled by the user for product
         * @param $item item object
         * @param $values list of item meta
         *
         */

        public function getCartItemsFromSession($item, $values, $key)
        {
            if (array_key_exists('wdm_edwiser_self_enroll', $values) && $values['wdm_edwiser_self_enroll'] != 'no') {
                $item['wdm_edwiser_self_enroll'] = $values['wdm_edwiser_self_enroll'];
            } else {
                $product_id = $values['product_id'];
                $post_meta = get_post_meta($product_id, 'product_options', true);
                if (isset($post_meta['moodle_course_group_purchase']) && !empty($post_meta['moodle_course_group_purchase'])) {
                    $item['wdm_edwiser_self_enroll'] = 'no';
                }
            }
            return $item;
            unset($key);
        }
        /*
         * Setting cart item data for checking if group registration is checked by user
         * @param $cart_item_meta cart item meta data
         * @param $product_id product id added in cart
         */

        public function wdmLdAddCartItemCustomDataSave($cart_item_meta, $product_id)
        {
            if (isset($_POST['wdm_edwiser_self_enroll']) && $_POST['wdm_edwiser_self_enroll'] != '') {
                $cart_item_meta['wdm_edwiser_self_enroll'] = $_POST['wdm_edwiser_self_enroll'];
            }
            return $cart_item_meta;
            unset($product_id);
        }
        /*
         * Hiding select quantity using js,it will be displayed only if user checked group registration checkbox
         */

        public function wdmLdWoocommerceBeforeAddToCartButton()
        {
            global $post;
            $product_id = $post->ID;
            $post_meta = get_post_meta($product_id, 'product_options', true);
            if (isset($post_meta['moodle_course_group_purchase']) && !empty($post_meta['moodle_course_group_purchase'])) {
                if ('on' == $post_meta['moodle_course_group_purchase']) {
                    ?>
                    <div class="wdm_edwiser_bulk_purchase">
                        <input type="checkbox" name="wdm_edwiser_self_enroll" id="wdm_edwiser_self_enroll" >
                        <?php echo apply_filters('wdm_edwiser_bulk_purchase_label', __('Enable Group Purchase', 'ebbp-textdomain'));
                        ?>
                    </div>
                    <?php
                }
            }
        }

        /**
         * Provides the functionality to get the group product of the user.
         *
         * @param int $userId user id whose group product information required.
         *
         * @return array Array of the group product id's.
         *
         * @since 1.0.1
         *
         */
        public function getGroupPrducts($userId)
        {
            $group_products = get_user_meta($userId, 'group_products', true);
            if (!isset($group_products) || empty($group_products)) {
                $group_products = array();
            }

            return $group_products;
        }

        public function productQuantityAfterOrderComplete($items, $oldProductArr)
        {
            $newProductArr = [];
            foreach ($items as $item => $property) {
                foreach ($oldProductArr as $key => $value) {
                    if ($property['product_id'] == $key) {
                        $newProductArr[$key] = $value + $property['qty'];
                    }
                }
                unset($item);
            }
            return $newProductArr;
        }

        /**
         * Function to get the courses associated with a particular product
         * @param number $productId
         * @return array $courses
         */
        public function wdmCoursesAssociatedWithProduct($productId)
        {
            global $wpdb;
            $tbl_name = $wpdb->prefix . "woo_moodle_course";
            $query = $wpdb->prepare("SELECT DISTINCT `moodle_post_id` FROM `{$tbl_name}` WHERE `product_id` = %d ", $productId);
            $courses = $wpdb->get_col($query);
            return $courses;
        }

        //v1.1.1
        /**
         * Function to enroll previous user to new courses when new product is added to particular cohort
         * @param array $enrolledUsers, number %courseId, number $enrolledBy, number $productId, String $cohortName
         */
        public function wdmUpdateMoodleEnrollment($enrolledUsers, $courseId, $enrolledBy, $productId, $cohortName)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . "moodle_enrollment";
            $userRole = "Student";
            foreach ($enrolledUsers as $user) {
                $wpdb->insert(
                    $tableName,
                    array(
                    'user_id' => $user,
                    'course_id' => $courseId,
                    'role_id' => 5,
                    'time' => date("Y-m-d h:i:s"),
                    'enrolled_by' => $enrolledBy,
                    'product_id' => $productId,
                    'mdl_cohort_id' => $cohortName,
                    'role' => $userRole
                        ),
                    array(
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                        )
                );
            }
        }

        /**
         * Update the cohort when new products are added to cohort
         * @param $order_id , $order_data
         */
        public function wdmUpdateCohortInfo($order_data, $order_id)
        {
            $mdlCohortId = $order_data['cohortId'];
            $order = new \WC_Order($order_id);
            $user = $order->get_user();
            $cuserId = $user->ID;

            global $wpdb;

            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT PRODUCTS, COURSES, COHORT_NAME, COHORT_MANAGER FROM $tableName WHERE MDL_COHORT_ID = %d", $mdlCohortId);

            $results = $wpdb->get_row($query);
            $products = unserialize($results->PRODUCTS);
            $courses = unserialize($results->COURSES);
            $cohortName = $results->COHORT_NAME;
            $cohortManagerId = $results->COHORT_MANAGER;
            $products_list = array_keys($products);
            $courses_list = array_values($courses);

            unset($order_data['cohortId']);

            // Getting list of users who are already enrolled for cohort
            $tableName = $wpdb->prefix . "moodle_enrollment";
            $query = $wpdb->prepare("SELECT DISTINCT user_id FROM $tableName WHERE enrolled_by = %d AND MDL_COHORT_ID = %d", $cuserId, $mdlCohortId);

            $enrolledUsers = $wpdb->get_col($query);
            foreach ($order_data as $product => $quantity) {
                if (in_array($product, $products_list)) {
                    $products[$product] += $quantity;
                } else {
                    $products[$product] = intval($quantity) - count($enrolledUsers);
                }
                $courses_product = $this->wdmCoursesAssociatedWithProduct($product);

                $unenrolledCourses = array_diff($courses_product, $courses_list);
                $cohrtManager = new BPManageCohort();
                $cohrtManager->enrollCohortIntoCourses($unenrolledCourses, $cohortName, $cohortManagerId);
                foreach ($courses_product as $course) {
                    if (!in_array($course, $courses_list) && !in_array($course, $courses)) {
                        array_push($courses, $course);
                        // Enrolling previous user to new courses
                        if (!empty($enrolledUsers)) {
                            $this->wdmUpdateMoodleEnrollment($enrolledUsers, $course, $cuserId, $product, $cohortName);
                        }
                    }
                }
            }
            // Update bp_cohort_info
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("UPDATE " . $tableName . " SET PRODUCTS = '" . serialize($products) . "', COURSES = '" . serialize($courses) . "' WHERE MDL_COHORT_ID = %d", $mdlCohortId);
            $wpdb->query($query);
        }

        /**
         * Save the product qty in user meta.
         *
         * @param type $order_id
         */
        public function wdmSaveProductQty($order_id)
        {
            $flagBulkPurchase = false;
            $order = new \WC_Order($order_id);
            $items = $order->get_items();
            //v1.1.1 for compatibility
            $user = $order->get_user();
            $userId = $user->ID;
            $emailArgs = array("user_email" => $user->user_email, "order_id" => $order_id);
            $orderMeta = wc_get_order_item_meta($order_id, "Group Enrollment");
            foreach ($items as $itemId => $prop) {
                $orderMeta = wc_get_order_item_meta($itemId, "Group Enrollment");
                $itemProductId = wc_get_order_item_meta($itemId, "_product_id");

                /**
                 * Check is the group enroll is enabled
                 */
                if ($orderMeta == "no") {
                    $this->enrollUserToCourse($itemProductId, $order);
                } else {
                    $flagBulkPurchase = true;
                }
                $prop = $prop;
            }
            $order_data = get_post_meta($order_id, 'add_product_from_enroll_page', true);
            if (!empty($order_data)) {
                $this->wdmUpdateCohortInfo($order_data, $order_id);
            } else {
                if ($userId != 0) {
                    $this->bulkProductNames = array();
                    global $wpdb;
                    $tableName = $wpdb->prefix . "bp_cohort_info";
                    $query = "SELECT ID, COHORT_NAME, PRODUCTS, COURSES, INCOMP_ORD, SYNC  FROM  $tableName WHERE COHORT_MANAGER = $userId";
                    $results = $wpdb->get_results($query, ARRAY_A);
                    $cohrtManager = new BPManageCohort();
                    foreach ($results as $tableRow) {
                        $incompleteOrder = $tableRow['INCOMP_ORD'];
                        $incompleteOrder = unserialize($incompleteOrder);
                        $present = in_array($order_id, $incompleteOrder);
                        $productArray = unserialize($tableRow['PRODUCTS']);
                        $courses = $tableRow['COURSES'];
                        $courses = unserialize($courses);
                        if ($present) {
                            $productQuantity = $this->productQuantityAfterOrderComplete($items, $productArray);
                            if ($tableRow['SYNC']) {
                                $cohortDetails = array("success" => 1, "cohortName" => $tableRow['COHORT_NAME']);
                                $cohrtManager->updateCohortOnUserEnrollment($order, $userId, $cohortDetails, $courses, $productQuantity);
                            } else {
                                $cohortDetails = array("success" => 0, "cohortName" => $tableRow['COHORT_NAME']);
                                $cohrtManager->updateCohortOnUserEnrollment($order, $userId, $cohortDetails, $courses, $productQuantity);
                            }
                        }
                    }
                }
            }
            if ($flagBulkPurchase) {
                do_action("eb_bp_bulk_purchase_email", $emailArgs);
            }
        }

        /**
         * Enroll user into teh courses
         * @param type $itemProduct
         */
        private function enrollUserToCourse($productId, $order)
        {
            global $wpdb;
            $product_options = get_post_meta($productId, 'product_options', true);
            $tbl_name = $wpdb->prefix . 'moodle_enrollment';

            //enroll user in to the course.
            $user = $order->get_user();
            $order_user = $user->ID;
            $moodlePostCourseId = $product_options['moodle_post_course_id'];
            $args = array(
                'user_id' => $order_user,
                'courses' => $moodlePostCourseId,
                'unenroll' => 0,
                'suspend' => 0,
            );
            $isuserEnrolled = $this->isUserEnrolled($moodlePostCourseId, $order_user, $order);
            $course_enrolled = edwiserBridge\edwiserBridgeInstance()->enrollmentManager()->updateUserCourseEnrollment($args);
            if (isset($course_enrolled) && !empty($course_enrolled) && $isuserEnrolled) {
                $courses = '(' . implode(',', $moodlePostCourseId) . ')';
                $query = $wpdb->prepare("update `{$tbl_name}` set enrolled_by = %d, product_id = %d  where user_id = %d and course_id in {$courses}", $order_user, $productId, $order_user);
                $wpdb->query($query);
            }
        }

        /**
         * function to update the userrole on wordpress
         * @return [type] [description]
         */
        public function updateWordpressUserRole($userId)
        {
            if (!user_can($userId, 'manage_options')) {
                wp_update_user(array('ID' => $userId, 'role' => 'non_editing_teacher'));
            }
        }

        /**
         * Provides the functionality for the ssaving and updating the product
         * quntity data into the database on order completion.
         * @param type $group_products array of the purchased products to update the quntity
         * @param type $order the currant orders object
         * @param type $order_id currant  order id.
         */
        public function saveProductQuantity($group_products, $order, $order_id)
        {
            $group_products = $this->checkIsQuntityEmpty($group_products);
            $user = $order->get_user();
            $userId = $user->ID;
            if (!empty($group_products) && $this->isEbBpOrderMarkCompleted($userId, $order_id)) {
                update_user_meta($userId, 'group_products', $group_products);
            }
        }

        /**
         * Provides the functionality to check is the product array have
         * associated product quantity null or less than zero or zero then
         * remove the product form the gropu product array
         * @param $group_products array of group product.
         * @return array of the group product
         */
        private function checkIsQuntityEmpty($group_products)
        {
            foreach ($group_products as $key => $val) {
                if ($val == null || $val <= 0) {
                    unset($group_products[$key]);
                }
            }
            return $group_products;
        }

        /**
         * Provides the funcrtionality to set the order status compleated.
         *
         * @param Int $userId Id of the user who has placed the product order
         * @param Int $orderId order id to update the compleat status.
         *
         * @since 1.0.1
         *
         */
        private function setEbBpOrderStatus($userId, $orderId)
        {
            $ebBpOrders = get_user_meta($userId, "eb_bp_compleated_orders", true);
            if (is_array($ebBpOrders)) {
                $ebBpOrders[$orderId] = 1;
            } else {
                $ebBpOrders = array($orderId => 1);
            }
            update_user_meta($userId, 'eb_bp_compleated_orders', $ebBpOrders);
        }

        /**
         * Provides the functionality to check is user's order of product is
         * compleated previously or not.
         * @param Int $userId Id of the user who has placed the product order
         * @param Int $orderId order id to update the compleat status.
         *
         * @return boolean true if the order is marked completed else return false
         *
         * @since 1.0.1
         */
        private function isEbBpOrderMarkCompleted($userId, $orderId)
        {
            $ebBpOrders = get_user_meta($userId, "eb_bp_compleated_orders", true);
            $flag = true;
            if (is_array($ebBpOrders) && array_key_exists($orderId, $ebBpOrders) && $ebBpOrders[$orderId] == 1) {
                $flag = false;
            } else {
                $this->setEbBpOrderStatus($userId, $orderId);
            }
            return $flag;
        }

        /**
         * Provides the functionality to update the product quntity.
         *
         * @param array $product array of the products purchased by the user.
         * @param array $group_products Array of the group product
         * @param Int $product_id  Currant product id
         *
         * @return array Returns array of the is product updated and the updated product quntity.
         * Return flag true if the product quantity is updated otherwise false.
         *
         * @since 1.0.1
         *
         */
        public function updateProductQuntity($product, $group_products, $product_id)
        {
            $flag = false;
            $post_meta = get_post_meta($product['product_id'], 'product_options', true);
            if (isset($post_meta['moodle_course_group_purchase']) && !empty($post_meta['moodle_course_group_purchase'])) {
                if ('on' == $post_meta['moodle_course_group_purchase']) {
                    if (!isset($group_products[$product_id]) && empty($group_products[$product_id])) {
                        $group_products[$product_id] = 0;
                    }
                    $group_products[$product_id] = $group_products[$product_id] + $product['qty'];
                    $flag = true;
                }
            }
            return array("flag" => $flag, "product_qty" => $group_products[$product_id]);
        }

        /**
         * Check is user already enrolled for the all the courses in the product
         *
         * @param Int $moodlePostCourseId moodle course id.
         * @param Int $order_user user id who has orderd the product.
         * @param type $order object of the currant order
         * @return boolean return true if the user not enrolled to the course else return true.
         *
         * @since 1.0.1
         *
         */
        public function isUserEnrolled($moodlePostCourseId, $order_user, $order)
        {
            global $wpdb;
            $tbl_name = $wpdb->prefix . 'moodle_enrollment';
            $user = $order->get_user();
            $order_user = $user->ID;
            $courses = '(' . implode(',', $moodlePostCourseId) . ')';
            $query = $wpdb->prepare("SELECT course_id FROM `{$tbl_name}` WHERE  `user_id` = '%d' AND course_id in {$courses}", $order_user);
            $res = $wpdb->get_results($query);
            $enrlledArray = array();
            foreach ($res as $cId) {
                $enrlledArray[] = $cId->course_id;
            }
            $moodlePostCourseId = array_diff($moodlePostCourseId, $enrlledArray);
            if (empty($moodlePostCourseId)) {
                return false;
            } else {
                return true;
            }
        }

        public function ebEmailtmplContent($content)
        {
            $bulkProductNames = '{BULK_PRODUCT_NAMES}';
            $bulkEnrolPageUrl = '{BULK_ENROL_PAGE_URL}';

            // BULK_PRODUCT_NAMES.
            if (count($this->bulkProductNames)) {
                $bulkProductNames = implode(', ', $this->bulkProductNames);
            }

            // BULK_ENROL_PAGE_URL.
            $setting = get_option('eb_general');
            if (isset($setting['mucp_group_enrol_page_id'])) {
                $status = get_post_status($setting['mucp_group_enrol_page_id']);
                if ($status != 'trash') {
                    $url = get_permalink($setting['mucp_group_enrol_page_id']);
                } else {
                    $page = get_page_by_title('Enroll Students');
                    $url = get_permalink($page->ID);
                }
            }

            if ($url) {
                $bulkEnrolPageUrl = esc_url($url);
            }

            if (is_user_logged_in()) {
                $curUser = wp_get_current_user();
                $userName = $curUser->first_name;
            }

            $content = str_replace(
                array(
                "{FIRST_NAME}",
                '{BULK_PRODUCT_NAMES}',
                '{BULK_ENROL_PAGE_URL}'
                    ),
                array(
                $userName,
                $bulkProductNames,
                $bulkEnrolPageUrl
                    ),
                $content
            );
            $this->bulkProductNames = array();
            return $content;
        }
    }
}
new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserSelfEnroll();
