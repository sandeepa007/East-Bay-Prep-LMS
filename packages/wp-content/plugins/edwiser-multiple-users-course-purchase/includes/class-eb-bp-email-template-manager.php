<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  www.wisdmlabs.com
 * @since 1.0.0
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @author     WisdmLabs, India <support@wisdmlabs.com>
 */
if (!class_exists("EbBpTemplateManager")) {

    class EbBpTemplateManager
    {

        /**
         * Provides the functionality to handle the tempalte restore event
         * genrated by the Edwiser bridge plugin for the template.
         * Calles for the eb_reset_email_tmpl_content filter
         * @param array $args contains the tmpl_name(Key) and boolean value to restore the template or not
         * @return array of the tmpl_name(Key) and is_restored(boolean on sucessfull restored true, false othrewise.)
         */
        public function handleTemplateRestore($args)
        {
            $tmplKey = $args['tmpl_name'];
            switch ($tmplKey) {
                case "eb_emailtmpl_bulk_prod_purchase_notifn":
                    $value = $this->getBulkPurchaseDefaultNotification('eb_emailtmpl_bulk_prod_purchase_notifn', true);
                    break;
                case "eb_emailtmpl_student_enroll_in_cohort_notifn":
                    $value = $this->getBulkPurchaseCohortEnrollNotification('eb_emailtmpl_student_enroll_in_cohort_notifn', true);
                    break;
                case "eb_emailtmpl_student_unenroll_in_cohort_notifn":
                    $value = $this->getBulkPurchaseCohortUnEnrollNotification('eb_emailtmpl_student_unenroll_in_cohort_notifn', true);
                    break;
                default:
                    return $args;
            }
            $status = update_option($tmplKey, $value);
            if ($status) {
                $args['is_restored'] = true;
                return $args;
            } else {
                return $args;
            }
        }

        /**
         * Prepares the bulk product enrollment email notification template content
         * @param string $tmplId template key
         * @param boolean $restore true to restore the templates default contend by default false
         * @return array array of template subject and content
         */
        public function getBulkPurchaseDefaultNotification($tmplId, $restore = false)
        {
            $data = get_option($tmplId);
            if ($data && !$restore) {
                return $data;
            }
            $data = array(
                'subject' => __('Enroll student in bulk', 'ebbp-textdomain'),
                'content' => $this->getBulkProdPurchaseMailDefaultBody()
            );
            return $data;
        }

        /**
         * Prepares the user cohort enrollment email notification template content
         * @param string $tmplId template key
         * @param boolean $restore true to restore the templates default contend by default false
         * @return array array of template subject and content
         */
        public function getBulkPurchaseCohortEnrollNotification($tmplId, $restore = false)
        {
            $data = get_option($tmplId);
            if ($data && !$restore) {
                return $data;
            }
            $data = array(
                'subject' => __('You have been enrolled in course', 'ebbp-textdomain'),
                'content' => $this->getEnrolStudentsInCohortDefaultMailContent()
            );
            return $data;
        }

        /**
         * Prepares the user cohort unenrollment email notification template content
         * @param string $tmplId template key
         * @param boolean $restore true to restore the templates default contend by default false
         * @return array array of template subject and content
         */
        public function getBulkPurchaseCohortUnEnrollNotification($tmplId, $restore = false)
        {
            $data = get_option($tmplId);
            if ($data && !$restore) {
                return $data;
            }
            $data = array(
                'subject' => __('You have been unenrolled from cohort', 'ebbp-textdomain'),
                'content' => $this->getUnEnrolStudentsInCohortDefaultMailContent()
            );
            return $data;
        }

        /**
         * Prepares the bulk product purchase email body.
         * @return html bulk product purchase email template
         */
        private function getBulkProdPurchaseMailDefaultBody()
        {
            ob_start();
            ?>
            <div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
                <table id="template_container" style="padding-bottom: 20px; box-shadow: 0 0 0 3px rgba(0,0,0,0.025) !important; border-radius: 6px !important; background-color: #dfdfdf;" border="0" width="600" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="background-color: #1f397d; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
                                <h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">
                                    <?php _e('Start enrolling students in courses', 'ebbp-textdomain');
                                    ?>
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php _e('Hi {FIRST_NAME},', 'ebbp-textdomain');
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    _e('Thank you for purchasing bulk products.', 'ebbp-textdomain');
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    echo '{BULK_PRODUCT_LIST}';
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    printf(__("Associated courses %s", 'ebbp-textdomain'), ":");
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    echo "{BULK_PRODUCTS_COURSES}";
                                    ?>
                                </div>

                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    printf(__('You can enroll students in the purchased products from %s.', 'ebbp-textdomain'), '<span style="color: #0000ff;">{BULK_ENROL_PAGE_URL}</span>');
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; border-top: 0; -webkit-border-radius: 6px;" align="center" valign="top"><span style="font-family: Arial; font-size: 12px;">{SITE_NAME}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
            $content = ob_get_clean();

            return apply_filters('mucp_bulk_prod_purchase_content', $content);
        }

        /**
         * Prepares user cohort enrollment email body.
         * @return html user cohort enrollment email template content
         */
        private function getEnrolStudentsInCohortDefaultMailContent()
        {
            ob_start();
            ?>
            <div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
                <table id="template_container" style="padding-bottom: 20px; box-shadow: 0 0 0 3px rgba(0,0,0,0.025) !important; border-radius: 6px !important; background-color: #dfdfdf;" border="0" width="600" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="background-color: #1f397d; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
                                <h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">
                                    <?php
                                    printf(__('You have been successfully enrolled in %s', 'ebbp-textdomain'), '{COHORT_NAME}');
                                    ?>
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php _e('Hi {FIRST_NAME},', 'ebbp-textdomain');
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    printf(__("You have been enrolled by %s to courses", 'ebbp-textdomain'), "{COHORT_MANAGER_DISP_NAME}");
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    echo "{BULK_ENROLLED_COURSES}";
                                    ?>
                                </div>

                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    printf(__('You can access your courses from %s.', 'ebbp-textdomain'), '<span style="color: #0000ff;">{MY_COURSES_PAGE_LINK}</span>');
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; border-top: 0; -webkit-border-radius: 6px;" align="center" valign="top"><span style="font-family: Arial; font-size: 12px;">{SITE_NAME}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
            $content = ob_get_clean();
            return apply_filters('mucp_student_enroll_in_cohort_content', $content);
        }

        /**
         * Prepares user cohort unenrollment email body.
         * @return html user cohort unenrollment email template content
         */
        private function getUnEnrolStudentsInCohortDefaultMailContent()
        {
            ob_start();
            ?>
            <div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
                <table id="template_container" style="padding-bottom: 20px; box-shadow: 0 0 0 3px rgba(0,0,0,0.025) !important; border-radius: 6px !important; background-color: #dfdfdf;" border="0" width="600" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="background-color: #1f397d; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
                                <h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">
                                    <?php
                                    printf(__('You have been unenrolled from %s', 'ebbp-textdomain'), '{COHORT_NAME}');
                                    ?>
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php _e('Hi {FIRST_NAME},', 'ebbp-textdomain');
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    printf(__("You have been unenrolled by %s from courses", 'ebbp-textdomain'), "{COHORT_MANAGER_DISP_NAME}");
                                    ?>
                                </div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
                                <div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">
                                    <?php
                                    echo "{COHORT_CURRENT_USER_COURSES}";
                                    ?>
                                </div>                                
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; border-top: 0; -webkit-border-radius: 6px;" align="center" valign="top"><span style="font-family: Arial; font-size: 12px;">{SITE_NAME}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
            $content = ob_get_clean();
            return apply_filters('mucp_student_unenroll_in_cohort_content', $content);
        }

        /**
         * Add email templates in the EB email list.
         *
         * @since 1.1.0
         */
        public function ebTemplatesList($list)
        {
            $list['eb_emailtmpl_bulk_prod_purchase_notifn'] = __('Bulk product purchase(courses)', 'ebbp-textdomain');
            $list['eb_emailtmpl_student_enroll_in_cohort_notifn'] = __('User enrolled in cohort', 'ebbp-textdomain');
            $list['eb_emailtmpl_student_unenroll_in_cohort_notifn'] = __('User unenrolled from cohort', 'ebbp-textdomain');
            return $list;
        }

        /**
         * Add email template constants.
         *
         * @since 1.1.0
         */
        public function ebTemplatesConstants($constants)
        {
            $constants["Bulk product purchase(courses)"]['{BULK_ENROL_PAGE_URL}'] = __('Enroll Students Page URL', 'ebbp-textdomain');
            $constants["Bulk product purchase(courses)"]['{BULK_PRODUCT_LIST}'] = __('Name of the products purchased in bulk', 'ebbp-textdomain');
            $constants["Bulk product purchase(courses)"]['{BULK_PRODUCTS_COURSES}'] = __('List of the courses associated with the product', 'ebbp-textdomain');
            $constants["User enrolled in cohort"]['{COHORT_NAME}'] = __('Enrolled courses list', 'ebbp-textdomain');
            $constants["User enrolled in cohort"]['{BULK_ENROLLED_COURSES}'] = __('Enrolled courses list', 'ebbp-textdomain');
            $constants["User enrolled in cohort"]['{COHORT_MANAGER_DISP_NAME}'] = __('Group manager display name', 'ebbp-textdomain');
            $constants["User unenrolled from cohort"]['{COHORT_NAME}'] = __('Group name', 'ebbp-textdomain');
            $constants["User unenrolled from cohort"]['{COHORT_CURRENT_USER_COURSES}'] = __('Cohort unenrolled courses list for users', 'ebbp-textdomain');
            return $constants;
        }

        /**
         * Callback for the eb_emailtmpl_content_before filter
         * @param array $data array of the default arguments provided by the send email action
         * and unparsed content
         * @return array returns the array of the default arguments and parsed content
         */
        public function emailTemplateParser($data)
        {
            $args = $data['args'];
            if (empty($args) || count($args) <= 0) {
                $args = array(
                    "product_id" => "1",
                    "mdl_cohort_id" => "1",
                    "order_id" => 231,
                    "cohort_manager_id" => 1,
                );
            }
            $tmplContent = $data['content'];
            $tmplConst = $this->getTmplConstant($args);
            foreach ($tmplConst as $const => $val) {
                $tmplContent = str_replace($const, $val, $tmplContent);
            }
            return array("args" => $args, "content" => $tmplContent);
        }

        /**
         * Provides the functionality to get the values for the email temaplte constants
         *
         * @param array $args array of the default values for the constants to
         * prepare the email template content
         *
         * @return array returns the array of the email temaplte constants with
         * associated values for the constants
         */
        private function getTmplConstant($args)
        {
            $constants['{BULK_ENROL_PAGE_URL}'] = $this->getBulkEnrollPageUrl();
            $constants['{BULK_PRODUCT_LIST}'] = $this->getProductList($args);
            $constants['{BULK_PRODUCTS_COURSES}'] = $this->getProductsCourses($args);
            $constants['{BULK_ENROLLED_COURSES}'] = $this->getCohortCourses($args);
            $constants['{COHORT_MANAGER_DISP_NAME}'] = $this->getManagerName($args);
            $constants['{COHORT_NAME}'] = $this->getCohortName($args);
            $constants['{COHORT_CURRENT_USER_COURSES}'] = $this->getCohortUnenrolledCourses($args);
            return $constants;
        }

        /**
         * Provides the functionality to get the enrolluser page url
         * @return link for the enroll user page
         */
        private function getBulkEnrollPageUrl()
        {
            $generalSettings = get_option("eb_general");
            $bulkEnrollPageId = $generalSettings['mucp_group_enrol_page_id'];
            return "<a href='" . get_permalink($bulkEnrollPageId) . "'>" . __('Enroll Students', 'ebbp-textdomain') . "</a>";
        }

        /**
         * Provides the functionality to get the product name by using product id
         * @param type $args default arguments for the send email notification
         * @return string returns the product id
         */
        private function getProductList($args)
        {
            ob_start();
            ?>
            <style>
                .wdm-emial-tbl-body{
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                }
                .wdm-emial-tbl-body thead{
                    background: #1f397d;
                    color: white;
                }
                .wdm-emial-tbl-body th,
                .wdm-emial-tbl-body td{
                    border: 1px solid #000000;
                    text-align: left;
                    padding: 8px;
                }
                .wdm-emial-tbl-body tbody{
                    background: white;
                }
            </style>
            <table border="0" cellspacing="0" class="wdm-emial-tbl-body" style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                <thead style="background: #1f397d;color: white;">
                    <tr>
                        <th style="border: 1px solid #000000;text-align: left;padding: 8px;">Product Name</th>
                        <th style="border: 1px solid #000000;text-align: left;padding: 8px;">Quantity</th>
                    </tr>
                </thead>
                <tbody style="background: white;">
                    <?php
                    if (isset($args['order_id'])) {
                        $order = new \WC_Order($args['order_id']);
                        $items = $order->get_items();
                        ?>
                        <?php
                        foreach ($items as $prop) {
                            if ($prop['qty'] > 1) {
                                ?>
                                <tr>
                                    <td style="border: 1px solid #000000;text-align: left;padding: 8px;"><?php echo get_the_title($prop['product_id']); ?></td>
                                    <td style="border: 1px solid #000000;text-align: left;padding: 8px;"><?php echo $prop['qty']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td style="border: 1px solid #000000;text-align: left;padding: 8px;"><?php _e("Test Product", 'ebbp-textdomain'); ?></td>
                            <td style="border: 1px solid #000000;text-align: left;padding: 8px;">5</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            return ob_get_clean();
        }

        /**
         * Provides the functionality to get the product associated courses by
         * using product id
         * @param type $args default arguments for the send email notification
         * @return string returns the list of the courses in the products
         */
        private function getProductsCourses($args)
        {
            if (!isset($args['order_id'])) {
                return "";
            }
            $orderId = $args['order_id'];
            $order = new \WC_Order($orderId);
            $products = $order->get_items();
            $data = "<div>";
            foreach ($products as $product) {
                $prodName = $product['name'];
                $prodId = $product['product_id'];
                $courses = get_post_meta($prodId, "product_options", true);
                if (!isset($courses['moodle_post_course_id']) && count($courses['moodle_post_course_id'])) {
                    continue;
                }
                $data .= "<div><p><strong>$prodName</strong></p><ol>";
                foreach ($courses['moodle_post_course_id'] as $courseId) {
                    $data.="<li><a href=" . get_permalink($courseId) . ">" . get_the_title($courseId) . "</a></li>";
                }
                $data.="</ol></div>";
            }
            $data.="</div>";
            return $data;
        }

        /**
         * Provides the functionality to get the chohort associated courses by
         * using cohort id
         * @param type $args default arguments for the send email notification
         * @return string returns the list of the courses in the cohort
         */
        private function getCohortCourses($args)
        {
            if (!isset($args['mdl_cohort_id'])) {
                return "";
            }
            $cohortId = $args['mdl_cohort_id'];
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "select COURSES from $tblCohortInfo where MDL_COHORT_ID='$cohortId'";
            $results = $wpdb->get_row($stmt);
            //v1.1.1
            $results = unserialize($results->COURSES);
            $outPut = "<div><ul>";
            foreach ($results as $courseId) {
                $outPut.="<li>" . get_the_title($courseId) . "</li>";
            }
            $outPut.="</ul></div>";
            return $outPut;
        }

        /**
         * Provides the functionality to get the chohort manager display name
         * using cohort manager id
         * @param type $args default arguments for the send email notification
         * @return string returns the cohort manager display name
         */
        private function getManagerName($args)
        {
            if (!isset($args["cohort_manager_id"])) {
                return "";
            }
            $managerId = $args["cohort_manager_id"];
            $manager = get_userdata($managerId);
            return $manager->display_name;
        }

        /**
         * Provides the functionality to get the chohort name
         * @param type $args default arguments for the send email notification
         * @return string returns the cohort name
         */
        private function getCohortName($args)
        {
            if (isset($args['mdl_cohort_id'])) {
                global $wpdb;
                $cohortId = $args['mdl_cohort_id'];
                $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
                $stmt = "select COHORT_NAME from $tblCohortInfo where MDL_COHORT_ID='$cohortId'";
                $cohortName = $wpdb->get_var($stmt);
                $managerName = get_userdata($args['cohort_manager_id']);
                return str_replace($managerName->user_login . "_", "", $cohortName);
            }
            return "cohort";
        }

        /**
         * Provides the functionality to get the chohort associated courses by
         * using cohort id
         * @param type $args default arguments for the send email notification
         * @return string returns the list of the courses in the cohort
         */
        private function getCohortUnenrolledCourses($args)
        {
            if (!isset($args['mdl_cohort_id'])) {
                return "";
            }
            $cohortId = $args['mdl_cohort_id'];
            global $wpdb;
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmt = "select COURSES from $tblCohortInfo where MDL_COHORT_ID='$cohortId'";
            $results = $wpdb->get_row($stmt);
            //v1.1.1
            $results = unserialize($results->COURSES);
            $outPut = "<div><ol>";
            foreach ($results as $courseId) {
                $outPut.="<li>" . get_the_title($courseId) . "</li>";
            }
            $outPut.="</ol></div>";
            return $outPut;
        }
    }
}
