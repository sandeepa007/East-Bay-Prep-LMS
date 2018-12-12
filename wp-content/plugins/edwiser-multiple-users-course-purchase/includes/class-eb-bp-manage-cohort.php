<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists("EBManageCohort")) {

    class BPManageCohort
    {
        /**
         * Update the user profile company information on the woocomerce checkout
         * @param Number $userId user id.
         * @since 1.2.0
         */
        public function updateUserProfile($userId)
        {
            if (isset($_POST["cohort_name"])) {
                update_user_meta($userId, "cohort_name", $_POST["cohort_name"]);
            }
        }

        /**
         * Checks is the current order contains the Edwiser bridge courses
         * @param type $fields Array of the woocommerce checkout page fields
         * @return array of the woocommerce checkout page fields
         * @since 1.2.0
         */
        public function mandatoryCompanyFiled($fields)
        {
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();

            if ($this->checkIsCoursesProduct($items)) {
                $fields['billing_company'] = array(
                    'label' => __('Company', 'woothemes'),
                    'placeholder' => __('Company', 'woothemes'),
                    'required' => true,
                    'class' => array('billing-phone'),
                    'priority' => 45
                );
            }
            return $fields;
        }

        /**
         * Provides the functionality to check is the current product contains the Edwiser Bridge course.
         * @param array $items product items
         * @return boolean
         * @since 1.2.0
         */
        private function checkIsCoursesProduct($items)
        {
            foreach ($items as $item) {
                $prodId = $item['product_id'];
                $pordMeta = get_post_meta($prodId, 'product_options', true);
                if (isset($pordMeta['moodle_course_group_purchase']) && $pordMeta['moodle_course_group_purchase'] == "on" && $item['quantity'] > 1) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Callback for the order checkout complete
         * This will process the order and update the cohort info into the databse.
         * @param Number $orderId the order ID.
         */
        public function handleOrderPlaced($orderId)
        {
            // Getting the session data
            $session_data = WC()->session->get('add_product_from_enroll_page');
            if (WC()->session->get('eb-bp-create-same-product')) {
                WC()->session->set('eb-bp-create-same-product', 0);
            }

            $order = wc_get_order($orderId);
            $courses = $this->getEBCoursesIdsForOrder($order);
            // if $courses then the order has bulk purchase product
            $products = $this->getBulkProductArrayFromOrder($orderId);
            $userId = get_post_meta($orderId, '_customer_user', true);
            if ($products['bulkProduct']) {
                //This for loop is for the multiple products added in the cart from enroll-students page add-quantity button
                    //if cohort name field exist then create only one group else create diffrent groups.
                if (isset($session_data[0]['cohort_id'])) {
                    foreach ($session_data as $key => $cohortDetails) {
                        $cohortName = $this->getCohortNameById($cohortDetails['cohort_id']);
                        $this->updateCohortInfo($courses, $orderId, $userId, $products['product'], $cohortName, true);
                    }
                } elseif (isset($_POST["cohort_name"]) && !empty($_POST["cohort_name"])) {
                    $cohortName = $_POST["cohort_name"];
                    $this->updateCohortInfo($courses, $orderId, $userId, $products['product'], $cohortName, false);
                } else {
                    foreach ($products['product'] as $key => $value) {
                        $value = $value;
                        $arrProduct = [];
                        $cohortName = get_the_title($key);
                        $arrProduct[$key] = 0;
                        $courses = $this->getCoursesFromProduct($key);
                        $this->updateCohortInfo($courses, $orderId, $userId, $arrProduct, $cohortName);
                    }
                }
            }

            // Updating the order meta
            if (!empty($session_data)) {
                if (empty(wc_get_order_item_meta($orderId, 'add_product_from_enroll_page', true))) {
                    update_post_meta($orderId, 'add_product_from_enroll_page', $session_data);
                }
                WC()->session->__unset('add_product_from_enroll_page');
            }
        }

        private function getCohortNameById($cohortId)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "select COHORT_NAME from $tblCohortInfo where MDL_COHORT_ID='$cohortId'";
            return $wpdb->get_var($stmt);
        }

        public function getCoursesFromProduct($productId)
        {
            global $wpdb;
            $tableName = $wpdb->prefix."woo_moodle_course";
            $query = "SELECT moodle_post_id FROM $tableName WHERE product_id = $productId";
            $result = $wpdb->get_col($query);
            return $result;
        }

        /**
         * function to get products and its quantity
         * @param  [int] $orderId woocommerce order id
         * @return [array]          associative array of product and its quantity
         */
        public function getBulkProductArrayFromOrder($orderId)
        {
            $orderObj = new \WC_Order($orderId);
            $order_item = $orderObj->get_items();
            $productArray = [];
            $bulkProduct = 0;
            foreach ($order_item as $productMeta) {
                $product_options = get_post_meta($productMeta["product_id"], 'product_options', true);
                if (isset($product_options['moodle_course_group_purchase']) && $product_options['moodle_course_group_purchase'] == "on") {
                    if (isset($productMeta['Group Enrollment']) && $productMeta['Group Enrollment'] == 'yes') {
                        $bulkProduct = 1;
                        $productArray[$productMeta['product_id']] = 0;
                    }
                }
            }
            return array("bulkProduct" => $bulkProduct, "product" => $productArray);
        }

        /**
         * Provides the functionality to insert the cohort information into the databse.
         * @param type $courseIDs Array of the associated course ids in order
         * @param type $orderId the order id
         * @param type $userId id of the user whoo have placed order.
         * @since 1.2.0
         */
        public function updateCohortInfo($courseIDs, $orderId, $userId, $products, $cohortName, $useSameCohort = false)
        {
            global $wpdb;
            $orders = array($orderId);
            $courses = $courseIDs;
            $isCohortExist = $this->addInSameCohort($cohortName, $courseIDs);
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $products = serialize($products);
            if ($useSameCohort) {
                $isCohortExist["cohort_name"]=$cohortName;
                $isCohortExist["add_in_same_cohort"]=true;
            }
            if ($isCohortExist["add_in_same_cohort"]==true) {
                $cohortName = $isCohortExist["cohort_name"];
                $stmt = "SELECT `INCOMP_ORD`,`COURSES` FROM $tblCohortInfo where COHORT_NAME='$cohortName'";
                $result = $wpdb->get_results($stmt, ARRAY_A);
                if (count($result) > 0) {
                    $orders = $this->unSerialize($result[0]["INCOMP_ORD"]);
                    array_push($orders, $orderId);
                } else {
                    $orders=array($orderId);
                }
                $wpdb->update(
                    $tblCohortInfo,
                    array(
                        "COHORT_MANAGER" => $userId,
                        "INCOMP_ORD" => serialize($orders),
                    ),
                    array(
                    "COHORT_NAME" => stripslashes($isCohortExist["cohort_name"])
                        )
                );
            } else {
                $cohortName = $this->genrateCohortName($cohortName, $userId);
                $wpdb->insert($tblCohortInfo, array(
                    "COHORT_NAME" => stripslashes($cohortName),
                    "PRODUCTS" => $products,
                    "COURSES" => serialize($courses),
                    "COHORT_MANAGER" => $userId,
                    "INCOMP_ORD" => serialize($orders),
                ));
            }
        }

        /**
         * Unserializas the string and prints the array
         * @param type $serialize the serialized array
         * @return array
         *
         * @since 1.2.0
         */
        private function unSerialize($serialize)
        {
            $dataArray = unserialize($serialize);
            if (is_array($dataArray) && count($dataArray) > 0) {
                return $dataArray;
            }
            return array();
        }

        /**
         * Provides the functionality to get the associated courses in order
         * @param type $order object of the woocomerce order
         * @return Array of the courses IDs
         * @since 1.2.0
         */
        private function getEBCoursesIdsForOrder($order)
        {
            $list_of_course_ids = array();

            $items = $order->get_items(); //Get Item details

            foreach ($items as $single_item => $itemMeta) {
                $single_item = $single_item;
                $product_id = '';
                if (isset($itemMeta['product_id'])) {
                    $_product = wc_get_product($itemMeta['product_id']);

                    if ($_product && $_product->is_type('variable') && isset($itemMeta['variation_id'])) {
                        //The line item is a variable product, so consider its variation.
                        $product_id = $itemMeta['variation_id'];
                    } else {
                        $product_id = $itemMeta['product_id'];
                    }
                }

                if (is_numeric($product_id)) {
                    $product_options = get_post_meta($product_id, 'product_options', true);
                    $group_purchase = 'off';
                    if ('on' == apply_filters('check_group_purchase', $group_purchase, $product_id)) {
                        if (!empty($product_options) && isset($product_options['moodle_post_course_id']) && !empty($product_options['moodle_post_course_id']) && isset($itemMeta['Group Enrollment']) && $itemMeta['Group Enrollment'] == 'yes') {
                            $line_item_course_ids = $product_options['moodle_post_course_id'];
                            if (!empty($list_of_course_ids)) {
                                $list_of_course_ids = array_unique(array_merge($list_of_course_ids, $line_item_course_ids), SORT_REGULAR);
                            } else {
                                $list_of_course_ids = $line_item_course_ids;
                            }
                        }
                    }
                }
            }//foreach ends
            return $list_of_course_ids;
        }

        /**
         * function to create the array of only key values present in the bp_cohort_info table
         * @param  [type] $products [description]
         * @return [type]           [description]
         */
        public function createProductKeyArray($products)
        {
            $arr = [];
            foreach ($products as $key => $value) {
                $value = $value;
                array_push($arr, $key);
            }
            return $arr;
        }

        /**
         * Provides the functionality to check is the cohort exists for the user
         * @param type $userId user id (Manager) to check the cohort name
         * @return String returns the cohort name
         * @since 1.2.0
         */
        private function addInSameCohort($cohortName, $courseIDs)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT * FROM $tableName WHERE COHORT_NAME like '%s'", $cohortName . "%");
            $results = $wpdb->get_results($query, ARRAY_A);
            $newCohortName = null;
            foreach ($results as $res) {
                $existingCourses = unserialize($res['COURSES']);
                sort($existingCourses);
                sort($courseIDs);
                if ($existingCourses == $courseIDs) {
                    $newCohortName = $res['COHORT_NAME'];
                    break;
                }
            }

            if ($newCohortName !== null) {
                return array("add_in_same_cohort" => true, "cohort_name" => $newCohortName);
            }
            return array("add_in_same_cohort" => false);
        }

        /**
         * Provides the functionality for to check is the cohort exists for the user
         * otherwise genrate the new cohort name
         * @param type $cohortName name of the cohort
         * @return type
         * @since 1.2.0
         */
        private function genrateCohortName($cohortName, $userId)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT count(*) count FROM $tableName WHERE COHORT_MANAGER = %d AND COHORT_NAME LIKE %s", $userId, '%' . $cohortName . '%');
            $result = $wpdb->get_var($query);
            $userInfo = get_userdata($userId);
            $cohortName = $userInfo->user_login . "_" . $cohortName;
            if ($result > 0) {
                $cohortName = $cohortName . "_" . $result;
            }
            return $cohortName;
        }

        public function orderComplete($orderId)
        {
            $order = wc_get_order($orderId);
            $courseIds = $this->getEBCoursesIdsForOrder($order);
            $userId = get_post_meta($orderId, '_customer_user', true);
            if (!empty($courseIds) && $this->usersHasPendingOrders($userId)) {
                $this->updateCohortInfoOnOrderComplete($userId, $this->getPendingOrders($userId), $orderId);
            }
        }

        /**
         * Provides the functionality to check that the user has pending orders in the
         * cohort info table.
         * @param type $userId User id to check pending orders in cohort info table
         * @return boolean
         * @since 1.2.0
         */
        private function usersHasPendingOrders($userId)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "SELECT `INCOMP_ORD` FROM $tblCohortInfo where COHORT_MANAGER='$userId'";
            $result = $wpdb->get_results($stmt, ARRAY_A);
            if (count($result) > 0) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Provides the functionality for the update the order on the order compleated.
         * @param number $userId
         * @param Array $incomplOrd
         * @param number $currentOrdId
         * @since 1.2.0
         */
        private function updateCohortInfoOnOrderComplete($userId, $incomplOrd, $currentOrdId, $cohortName, $productArray)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $key = array_search($currentOrdId, $incomplOrd);


            if ($key !== false) {
                unset($incomplOrd[$key]);
            }
            $result = $wpdb->update(
                $tblCohortInfo,
                array(
                "COHORT_MANAGER" => $userId,
                "INCOMP_ORD" => serialize($incomplOrd),
                "PRODUCTS" => serialize($productArray),
                ),
                array(
                "COHORT_MANAGER" => $userId,
                "COHORT_NAME" => $cohortName,
                )
            );
            return $result;
        }

        private function getPendingOrders($userId)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "SELECT `INCOMP_ORD` FROM $tblCohortInfo where COHORT_MANAGER='$userId'";
            $result = $wpdb->get_results($stmt, ARRAY_A);
            return $this->unSerialize($result[0]["INCOMP_ORD"]);
        }



        public function getProductsFromCohortName($cohortName)
        {
            global $wpdb;
            $tableName = $wpdb->prefix."bp_cohort_info";
            $query = $wpdb->prepare("SELECT PRODUCTS FROM $tableName WHERE COHORT_NAME = %s", $cohortName);
            $prodcts = $wpdb->get_var($query);
            return $prodcts;
        }





        /**
         * Provides the functionality to get the cohort name for the user.
         * @param type $userId user id to get the cohort
         * @return cohort name if the cohort exist for the user otherwise false
         */

        /**
         *
         * @param  [type] $userId     [description]
         * @param  [type] $cohortName [description]
         * @param  [type] $flag       used to check if it is created while updating plugin or while placing order
         * @return [type]             [description]
         */
        public function createCohortOnMoodle($userId, $cohortName, $flag = 0)
        {

            /*code added for cohort id  starts here*/
            $reverseCohortname = strrev($cohortName);
            $occurrence = strpos($reverseCohortname, "_");

            if ($occurrence > 3 || $occurrence == 0) {
                $substr = "";
            } else {
                $substr = substr($reverseCohortname, 0, $occurrence);
                $substr = strrev($substr);
            }


            $products = unSerialize($this->getProductsFromCohortName($cohortName));

            error_log("cohortName.productArray".print_r($products, 1));

            //product array with product id as value
            $products = array_keys($products);
            // product ids connected with "_"
            $products = implode("_", $products);
            $user = get_userdata($userId);
            $userName = $user->user_login;
            $idnumber = $userName."_".$products."_".$substr;
            /*code added for cohort id ends here*/

            $moodleFunction = "core_cohort_create_cohorts";
            $args = array(
                "categorytype" => array("type" => "system", "value" => ""),
                "name" => $cohortName,
                "idnumber" => $idnumber,
                "descriptionformat" => 1,
                "description" => " ",
                "visible" => 1,
            );
            $ebConnectionHelper = BPManageCohort::getEBConnectionHelper();
            $responce = $ebConnectionHelper->connectMoodleWithArgsHelper($moodleFunction, array("cohorts" => array($args)));
            if (isset($responce["success"]) && $responce["success"] && $flag == 0) {
                $responseData = $responce["response_data"];
                $this->updateMoodleCohortId($cohortName, $responseData[0], $userId);
                $responce= true;
            } elseif (isset($responce["success"]) && $responce["success"] && $flag == 1) {
                $responce= $responce["response_data"];
            } elseif (isset($responce['response_message'])) {
                new BPAdminNoticess($responce['response_message'], 2);
                $responce= false;
            }
            return $responce;
        }

        public function enrollCohortIntoCourses($courses, $cohortName, $userId)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "SELECT `MDL_COHORT_ID` FROM $tblCohortInfo WHERE COHORT_MANAGER='$userId' AND COHORT_NAME='$cohortName'";
            $cohortId = $wpdb->get_var($stmt);
            $moodleFunction = "wdm_manage_cohort_enrollment";
            $connHelper = BPManageCohort::getEBConnectionHelper();

            foreach ($courses as $course) {
                $courseId = get_post_meta($course, "moodle_course_id");
                $connHelper->connectMoodleWithArgsHelper($moodleFunction, array("cohort" => array(array("courseId" => $courseId[0], "cohortId" => $cohortId))));
            }
        }

        public function updateCohortOnUserEnrollment($order, $userId, $cohortName, $courses, $productArray)
        {
            $cohortCreated = true;
            if (!$cohortName['success']) {
                $cohortCreated = $this->createCohortOnMoodle($userId, $cohortName['cohortName']);
            }
            if ($cohortCreated) {
                $this->enrollCohortIntoCourses($courses, $cohortName['cohortName'], $userId);
                $incomplOrd = $this->getPendingOrders($userId);
                $order_id = trim(str_replace('#', '', $order->get_order_number()));
                $this->updateCohortInfoOnOrderComplete($userId, $incomplOrd, $order_id, $cohortName['cohortName'], $productArray);
            }
        }

        /**
         *
         * @return Object returns the object of the EBConnectionHelper class from the edwiser bridge plugin.
         */
        public static function getEBConnectionHelper()
        {
            $ebLoader = \app\wisdmlabs\edwiserBridge\edwiserBridgeInstance();
            return \app\wisdmlabs\edwiserBridge\EBConnectionHelper::instance($ebLoader->getPluginName(), $ebLoader->getVersion());
        }

        /**
         * Checks is the cohort is synced with moodle
         * @param type $usrId cohort manager id
         * @param type $cohortName name of the cohort
         * @return boolean returns true if the user is sync with the moodle otherwise false.
         */
        private function updateMoodleCohortId($oldCohortName, $moodleResponce, $userId)
        {
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';

            $wpdb->update(
                $tblCohortInfo,
                array(
                    "COHORT_NAME" => $moodleResponce->name,
                    "MDL_COHORT_ID" => $moodleResponce->id,
                    "SYNC" => 1,
                    ),
                array(
                    "COHORT_NAME" => $oldCohortName,
                    "COHORT_MANAGER" => $userId,
                )
            );
        }

        /**
         * Provides the functionality to update the cohort name and user role on
         * course enrollment.
         *
         * @param Array $courses array of the course id's user has been enrolled.
         * @param String $cohortName name of the cohort user enrolled.
         * @param Number $userId user id who has beend enrolled to the courses.
         */
        public function updateEnrollmentRecords($courses, $cohortName, $userId)
        {
            global $wpdb;
            $moodleEnrollment = $wpdb->prefix . "moodle_enrollment";
            $query = $wpdb->prepare("update `{$moodleEnrollment}` set cohort_name = %s, role = %s  where user_id = %d and course_id in ({$courses})", $cohortName, "Manager", $userId);
            $wpdb->query($query);
        }
    }
}
