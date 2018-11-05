<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\BPDbBackUp')) {

    class BPDbBackUp
    {
        public function run()
        {
            $respMsg = "<div>";
            $msg = $this->checkDependencies();
            if (isset($msg['success']) && !$msg['success'] == 0) {
                $respMsg.=$msg['msg'];
                $DbBackUp = $this->mdlEnrollTblDbBackup();
                $respMsg.=$DbBackUp['msg'];
                $fileBackup = $this->backUpMoodleEnrollmentTableInFile();
                if ($DbBackUp == 1 || $fileBackup == 1) {
                    $respMsg .= '<span class="ebbp_migrate_notice_msg">' . __("Database Backup Completed", 'ebbp-textdomain') . '<span class="migrate-dashicons dashicons dashicons-yes"></span></span><br><br>';
                    wp_send_json_success($respMsg);
                } else {
                    $respMsg .= '<span class="ebbp_migrate_error_msg"><span class="dashicons dashicons-warning"></span>' . __("Unable to back up Database Please take database backup manually and then proceed.", 'ebbp-textdomain') . '</span><br><input id="ebbp-migrate-backup" class ="ebbp-migrate-button" type="button" name="ebbp-migrate-backup" value="Proceed"><br>';
                    wp_send_json_error($respMsg);
                }
            } else {
                wp_send_json_error($respMsg);
            }
        }

        private function checkDependencies()
        {
            $connection_options = get_option('eb_connection');
            $ebMoodleUrl = '';
            if (isset($connection_options['eb_url'])) {
                $ebMoodleUrl = $connection_options['eb_url'];
            }
            $ebMoodleToken = '';
            if (isset($connection_options['eb_access_token'])) {
                $ebMoodleToken = $connection_options['eb_access_token'];
            }
            $requestUrl = $ebMoodleUrl . '/webservice/rest/server.php?wstoken=';


            $responce=array("success"=>true,"msg"=>"");
            $moodleFunctionArray = array(
                "core_cohort_add_cohort_members",
                "core_cohort_create_cohorts",
                "core_role_assign_roles",
                "core_role_unassign_roles",
                "core_cohort_delete_cohort_members",
                "core_cohort_get_cohorts",
                "wdm_manage_cohort_enrollment",
            );
            $resp=array("success"=>1);
            ob_start();
            ?>
            <div>
                <h3>Checking moodle API calls</h3>
                <?php
                foreach ($moodleFunctionArray as $apiFunction) {
                    $requestUrl .= $ebMoodleToken . '&wsfunction=' . $apiFunction . '&moodlewsrestformat=json';

                    $response = wp_remote_post($requestUrl);
                    if (strpos($response['body'], 'accessexception') != false) {
                        echo '<span class="ebbp_migrate_error_msg"><span class="dashicons dashicons-warning"></span>' . __("Please Include all the needed apis on moodle site", 'ebbp-textdomain') . '</span><br><br>';
                        $resp['success']=0;
                    } else {
                        ?>                    
                        <p><?php echo $apiFunction . " API working successfully...."; ?></p>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
            $resp['msg']=  ob_get_clean();
            return $responce;
        }

        /**
         * function  to get take backup of moodle enrollment table on database
         * @return [type] [description]
         */
        private function mdlEnrollTblDbBackup()
        {
            global $wpdb;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $tblMdlEnroll = $wpdb->prefix . "moodle_enrollment";
            $backupTable = $wpdb->prefix . "moodle_enrollment_backup";
            $query = "SHOW TABLES LIKE '$backupTable'";
            $charsetCollate = $wpdb->get_charset_collate();
            $resp = array("success" => "1", "msg" => "<div><p>Backup is alreadytaken</p></div>");
            $var = $wpdb->get_var($query);
            $msg = "";
            if ($var != $backupTable) {
                $stmtBackUp = "CREATE TABLE $backupTable AS SELECT * FROM $tblMdlEnroll";
                $stmtChangeCollate = "ALTER TABLE $backupTable COLLATE $charsetCollate";
                $msg.="<div><h3>Creating backup tables</h3>";
                try {
                    $response = dbDelta($stmtBackUp);
                    $wpdb->query($stmtChangeCollate);
                    $msg.="<p>Creating backup table</p>";
                } catch (Exception $e) {
                    $query = "SET SESSION sql_mode = ''";
                    $msg.="<p>Stting SQL mode</p>";
                    $wpdb->query($query);
                    $response = dbDelta($stmtBackUp);
                    $wpdb->query($stmtChangeCollate);
                    $msg.="<p>Creating backup table</p>";
                }
                $response = $response[$backupTable];
                if (!strpos($response, 'Created')) {
                    $resp["success"] = false;
                    $msg = "<div><p>Failed to take the DB backup. YOu can try again or take the DB backup manually.</p>";
                } else {
                    $resp["success"] = true;
                }
                $resp["msg"] = $msg . "</div>";
            }
            return $resp;
        }

        /**
         * function to take backup of the moodle enrollment table in the file
         * @return [type] [description]
         */
        private function backUpMoodleEnrollmentTableInFile()
        {
            global $wpdb;
            $charsetCollate = $wpdb->get_charset_collate();
            $table = $wpdb->prefix . "moodle_enrollment";
            $query = "SELECT * FROM $table";
            $results = $wpdb->get_results($query, ARRAY_A);
            $path = wp_upload_dir();
            $text = "CREATE TABLE IF NOT EXISTS $table (
                id            mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id       int(11) NOT NULL,
                course_id     int(11) NOT NULL,
                role_id       int(11) NOT NULL,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                expire_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                enrolled_by => varchar(10),
                product_id => int(11),
                PRIMARY KEY id (id)
            ) $charsetCollate;";


            $file = fopen($path['basedir'] . "/moodle_enrollment_backup.sql", "w") or die("Unable to open file!");
            if ($file) {
                foreach ($results as $row) {
                    $text .= "INSERT INTO $table (user_id, course_id, role_id, 'time', expire_time, enrolled_by, product_id) VALUES (" . $row["user_id"] . ", " . $row["course_id"] . ", " . $row["role_id"] . ", " . $row["time"] . ", " . $row["expire_time"] . "," . $row['enrolled_by'] . ", " . $row["product_id"] . ");\n";
                    fwrite($file, $text);
                }
            } else {
                return 0;
            }
            fclose($file);
            return 1;
        }
    }
}
