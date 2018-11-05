<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\EnrollmentManager')) {

    class EnrollmentManager
    {
        public function addColumnsToManageEnrollTable($columnMembers)
        {
            add_action("eb_before_manage_user_enrollment_table", array($this, "addPopUpData"));

            $columnMembers['cohort'] = __('Cohort', 'ebbp-textdomain');
            return $columnMembers;
        }

        public function manageEnrollmentTableData($tableRecord)
        {
            global $wpdb;
            $tableRecord = array();
            $stmt = "SELECT * FROM {$wpdb->prefix}moodle_enrollment";
            $results = $wpdb->get_results($stmt);
            foreach ($results as $result) {
                $row = array();
                $row['user_id'] = $result->user_id;
                $row['user'] = getUserProfileURL($result->user_id);
                $row['course'] = '<a href="' . esc_url(get_permalink($result->course_id)) . '">' . get_the_title($result->course_id) . '</a>';
                $row['enrolled_date'] = $result->time;
                $row['ID'] = $result->id;
                $row['rId'] = $result->id;
                $row['course_id'] = $result->course_id;

                if ($result->mdl_cohort_id != null) {
                    $row['manage'] = false;
                    $str = '<div><p>' . $this->getCohortName($result->mdl_cohort_id) . '</p>
                         <lable class="ebbp-cohort-details-link" data-cohort-manager="'.$result->enrolled_by.'" data-mdl-cohort-id="'.$result->mdl_cohort_id.'" data-user-id="'.$result->user_id.'" data-record-id="' . $result->id . '">' . __("Details", 'ebbp-textdomain') . '</lable></div>';
                } else {
                    $row['manage'] = true;
                    $str = "---";
                }
                $row['cohort'] = $str;
                $row['enrolled_by'] = $result->enrolled_by;
                $tableRecord[] = $row;
            }

            return $tableRecord;
        }

        private function getCohortName($mdlCohortId){
            global $wpdb;
            $stmt = "SELECT COHORT_NAME FROM {$wpdb->prefix}bp_cohort_info where mdl_cohort_id='$mdlCohortId'";
            return $wpdb->get_var($stmt);
        }
        public function addPopUpData()
        {
            ?>
            <div class="mucp-cohort-details">
                <div id='mucp-cohort-details-dialog'>
                    <table border="0">
                        <tbody>
                            <tr>
                                <td class="eb-cohort-details-lable"><?php _e("Company Name :", 'ebbp-textdomain') ?></td>
                                <td id="eb-copany-name"></td>
                            </tr>
                            <tr>
                                <td class="eb-cohort-details-lable"> <?php _e("Cohort Manager :", 'ebbp-textdomain') ?></td>
                                <td id="eb-manager"></td>
                            </tr>
                            <tr>
                                <td class="eb-cohort-details-lable"><?php _e("Total Cohort Members :", 'ebbp-textdomain') ?></td>
                                <td id="eb-members"></td>
                            </tr>
                            <tr>
                                <td class="eb-cohort-details-lable manage-enrollment-table-courses"><?php _e("Associated Courses :", 'ebbp-textdomain') ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="eb-cohort-details-lable"></td>
                                <td id="eb-courses"></td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>

                    <table border="0">
                        <tbody>
                            <tr>
                                <td class="eb-cohort-details-lable"><?php _e("User Name :", 'ebbp-textdomain') ?></td>
                                <td id="eb-current-user"></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="cohort-details-notice">
                        <p> <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php _e("  Unenrolling users from the cohort will unenroll users from all the associated courses.", 'ebbp-textdomain') ?> </p>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}
