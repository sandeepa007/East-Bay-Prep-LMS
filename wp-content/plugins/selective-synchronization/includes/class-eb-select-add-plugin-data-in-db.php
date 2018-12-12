<?php

namespace ebSelectSync\includes;

if (!class_exists('EBSelectAddPluginDataInDB')) {
    class EBSelectAddPluginDataInDB
    {

        /**
         *
         * @var string Short Name for plugin.
         */
        public $plugin_short_name = "";

        /**
         *
         * @var string Slug to be used in url and functions name
         */
        public $plugin_slug = '';

        /**
         *
         * @var string stores the current plugin version
         */
        public $plugin_version = '';

        /**
         *
         * @var string Handles the plugin name
         */
        public $plugin_name = '';

        /**
         *
         * @var string  Stores the URL of store. Retrieves updates from
         *              this store
         */
        public $store_url = '';

        /**
         *
         * @var string  Name of the Author
         */
        public $author_name = '';

        public function __construct($plugin_data)
        {

            $this->author_name = $plugin_data['author_name'];
            $this->plugin_name = $plugin_data['plugin_name'];
            $this->plugin_short_name = $plugin_data['plugin_short_name'];
            $this->plugin_slug = $plugin_data['plugin_slug'];
            $this->plugin_version = $plugin_data['plugin_version'];
            $this->store_url = $plugin_data['store_url'];

            add_filter('eb_setting_messages', array($this, 'selectiveLicenseMessages'), 15, 1);
            add_filter('eb_licensing_information', array($this, 'selectiveLicenseInformation'), 15, 1);
            add_action('init', array($this, 'addData'), 5);
        }

        public function selectiveLicenseMessages($eb_licensing_msg)
        {
            //Get License Status
            $status = get_option('edd_' . $this->plugin_slug . '_license_status');

            // echo "status=".$status;
            // die();

            include_once(plugin_dir_path(__FILE__) . 'class-eb-select-get-plugin-data.php');
            $active_site = EBSelectGetPluginData::getSiteList($this->plugin_slug);

            $display = "";

            if (!empty($active_site) || $active_site != "") {
                $display = "<ul>" . $active_site . "</ul>";
            }

           // $license_key = trim(get_option('edd_' . $this->plugin_slug . '_license_key'));
             // if (isset($_POST['edd_' . $this->plugin_slug . '_license_key'])) {
                //Handle Submission of inputs on license page
        //         if (isset($_POST['edd_' . $this->plugin_slug . '_license_key']) && empty($_POST['edd_' . $this->plugin_slug . '_license_key'])) {
        //             //If empty, show error message
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('Please enter license key for %s.', 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         }
        // //      else if(!empty($_POST['edd_' . $this->plugin_slug .'_license_key']))
        // // {
        //     else if (  $status == 'valid' ) { //Valid license key

        //         //Valid license key
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License key for %s is activated.', 'selective_synchronization'), $this->plugin_name),
        //                 'updated'
        //             );
        //         } else if ($status !== false && $status == 'expired' && (!empty($display) || $display != "")) { //Expired license key
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License key for %s have been Expired. Please, Renew it. <br/>Your License Key is already activated at : ' . $display, 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status !== false && $status == 'expired') { //Expired license key
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License key for %s have been Expired. Please, Renew it.', 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status !== false && $status == 'disabled') { //Disabled license key
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License key for %s is Disabled.', 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status == 'invalid' && (!empty($display) || $display != "")) { //Invalid license key   and site
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License Key for %s is already activated at : ' . $display, 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status == 'invalid') { //Invalid license key
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('Please enter valid license key for %s.', 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status == 'site_inactive' && (!empty($display) || $display != "")) { //Invalid license key   and site inactive
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License Key for %s is already activated at : ' . $display, 'selective_synchronization'), $this->plugin_name),
        //                 'error'
        //             );
        //         } else if ($status == 'site_inactive') { //Site is inactive
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 __('Site inactive(Press Activate license to activate plugin)', 'selective_synchronization'),
        //                 'error'
        //             );
        //         } else if ($status == 'deactivated') { //Site is inactive
        //             add_settings_error(
        //                 'eb_' . $this->plugin_slug . '_errors',
        //                 esc_attr('settings_updated'),
        //                 sprintf(__('License Key for %s is deactivated', 'selective_synchronization'), $this->plugin_name),
        //                 'updated'
        //             );
        //         }
            // }
        // }
           // $license_key = trim(get_option('edd_' . $this->plugin_slug . '_license_key'));
            // if (isset($_POST['edd_' . $this->plugin_slug . '_license_key'])) {
                //Handle Submission of inputs on license page
            if (isset($_POST['edd_' . $this->plugin_slug . '_license_key']) && empty($_POST['edd_' . $this->plugin_slug . '_license_key'])) {
                //If empty, show error message
                add_settings_error(
                    'eb_' . $this->plugin_slug . '_errors',
                    esc_attr('settings_updated'),
                    sprintf(__('Please enter license key for %s.', 'selective_synchronization'), $this->plugin_name),
                    'error'
                );
            } else if ($status == 'valid') {
                //Valid license key
                    add_settings_error(
                        'eb_' . $this->plugin_slug . '_errors',
                        esc_attr('settings_updated'),
                        sprintf(__('License key for %s is activated.', 'selective_synchronization'), $this->plugin_name),
                        'updated'
                    );
            } else if ($status !== false && $status == 'expired') { //Expired license key
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('License key for %s have been Expired. Please, Renew it.', 'selective_synchronization'), $this->plugin_name),
                       'error'
                   );
            } else if ($status !== false && $status == 'disabled') { //Disabled license key
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('License key for %s is Disabled.', 'selective_synchronization'), $this->plugin_name),
                       'error'
                   );
            } else if ($status == 'invalid' && (!empty($display) || $display != "")) { //Invalid license key   and site
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('License Key for %s is already activated at : ' . $display, 'selective_synchronization'), $this->plugin_name),
                       'error'
                   );
            } else if ($status == 'invalid') { //Invalid license key
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('Please enter valid license key for %s.', 'selective_synchronization'), $this->plugin_name),
                       'error'
                   );
            } else if ($status == 'site_inactive' && (!empty($display) || $display != "")) { //Invalid license key   and site inactive
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('License Key for %s is already activated at : ' . $display, 'selective_synchronization'), $this->plugin_name),
                       'error'
                   );
            } else if ($status == 'site_inactive') { //Site is inactive
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       __('Site inactive(Press Activate license to activate plugin)', 'selective_synchronization'),
                       'error'
                   );
            } else if ($status == 'deactivated') { //Site is inactive
                   add_settings_error(
                       'eb_' . $this->plugin_slug . '_errors',
                       esc_attr('settings_updated'),
                       sprintf(__('License Key for %s is deactivated', 'selective_synchronization'), $this->plugin_name),
                       'updated'
                   );
            }
           
                ob_start();

                settings_errors('eb_' . $this->plugin_slug . '_errors');
            //settings_errors('eb_' . $this->plugin_slug . '_errors',false,true);
                $ss_setting_messages = ob_get_contents();
                ob_end_clean();
                return $eb_licensing_msg . $ss_setting_messages;
        }


        
        public function selectiveLicenseInformation($licensing_info)
        {

            $renew_link = get_option('eb_' . $this->plugin_slug . '_product_site');

            //Get License Status
            $status = get_option('edd_' . $this->plugin_slug . '_license_status');
            include_once(plugin_dir_path(__FILE__) . 'class-eb-select-get-plugin-data.php');

            $active_site = EBSelectGetPluginData::getSiteList($this->plugin_slug);

            $display = "";
            if (!empty($active_site) || $active_site != "") {
                $display = "<ul>" . $active_site . "</ul>";
            }

            $license_key = trim(get_option('edd_' . $this->plugin_slug . '_license_key'));


            // LICENSE KEY
            if (($status == "valid" || $status == "expired") && (empty($display) || $display == "")) {
                $license_key_html = '<input id="edd_' . $this->plugin_slug . '_license_key" name="edd_' . $this->plugin_slug . '_license_key" type="text" class="regular-text" value="' . esc_attr($license_key) . '" readonly/>';
            } else {
                $license_key_html = '<input id="edd_' . $this->plugin_slug . '_license_key" name="edd_' . $this->plugin_slug . '_license_key" type="text" class="regular-text" value="' . esc_attr($license_key) . '" />';
            }


            //LICENSE STATUS

            /*added by wisdmlabs after psr2*/
            $license_status=$this->getLicenseStatus($status, $this->plugin_slug, $display);

            // if ($status !== false && $status == 'valid') {
            //     $license_status = '<span style="color:green;">' . __('Active', 'selective_synchronization') . '</span>';
            // } else if (get_option('edd_' . $this->plugin_slug . '_license_status') == 'site_inactive') {
            //     $license_status = '<span style="color:red;">' . __('Not Active', 'selective_synchronization') . '</span>';
            // } else if (get_option('edd_' . $this->plugin_slug . '_license_status') == 'expired' && (!empty($display) || $display != "")) {
            //     $license_status = '<span style="color:red;">' . __('Expired', 'selective_synchronization') . '</span>';
            // } else if (get_option('edd_' . $this->plugin_slug . '_license_status') == 'expired') {
            //     $license_status = '<span style="color:green;">' . __('Active', 'selective_synchronization') . '</span>';
            // } elseif (get_option('edd_' . $this->plugin_slug . '_license_status') == 'invalid') {
            //     $license_status = '<span style="color:red;">' . __('Invalid Key', 'selective_synchronization') . '</span>';
            // } else {
            //     $license_status = '<span style="color:red;">' . __('Not Active ', 'selective_synchronization') . '</span>';
            // }

            //Activate License Action Buttons
            ob_start();
            wp_nonce_field('edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce');
            $nonce = ob_get_contents();
            ob_end_clean();
            if ($status !== false && $status == 'valid') {
                $buttons = '<input type="submit" class="button-primary" name="edd_' . $this->plugin_slug . '_license_deactivate" value="' . __('Deactivate License"', 'selective_synchronization') . '/>';
            } elseif ($status == 'expired' && (!empty($display) || $display != "")) {
                $buttons = '<input type = "submit" class = "button-primary" name = "edd_' . $this->plugin_slug . '_license_activate" value = "' . __('Activate License', 'selective_synchronization') . '"/>';
                $buttons .= ' <input type = "button" class = "button-primary" name = "edd_' . $this->plugin_slug . '_license_renew" value = "' . __('Renew License', 'selective_synchronization') . '" onclick = "window.open( \'' . $renew_link . '\')"/>';
            } elseif ($status == 'expired') {
                $buttons = '<input type="submit" class="button-primary" name="edd_' . $this->plugin_slug . '_license_deactivate" value="' . __('Deactivate License', 'selective_synchronization') . '"/>';
                $buttons .= ' <input type="button" class="button-primary" name="edd_' . $this->plugin_slug . '_license_renew" value="' . __('Renew License', 'selective_synchronization') . '" onclick="window.open( \'' . $renew_link . '\' )"/>';
            } else {
                $buttons = '<input type="submit" class="button-primary" name="edd_' . $this->plugin_slug . '_license_activate" value="' . __('Activate License', 'selective_synchronization') . '"/>';
            }


            $info = array(
                'plugin_name' => $this->plugin_name,
                'plugin_slug' => $this->plugin_slug,
                'license_key' => $license_key_html,
                'license_status' => $license_status,
                'activate_license' => $nonce . $buttons
            );

            $licensing_info[] = $info;
            return $licensing_info;
        }
        /*added by wisdmlabs after psr2*/
        /*code starts here */
        public function getLicenseStatus($status, $plugin_slug, $display)
        {
            if ($status !== false && $status == 'valid') {
                $license_status = '<span style="color:green;">' . __('Active', 'selective_synchronization') . '</span>';
            } else if (get_option('edd_' . $plugin_slug . '_license_status') == 'site_inactive') {
                $license_status = '<span style="color:red;">' . __('Not Active', 'selective_synchronization') . '</span>';
            } else if (get_option('edd_' . $plugin_slug . '_license_status') == 'expired' && (!empty($display) || $display != "")) {
                $license_status = '<span style="color:red;">' . __('Expired', 'selective_synchronization') . '</span>';
            } else if (get_option('edd_' . $plugin_slug . '_license_status') == 'expired') {
                $license_status = '<span style="color:green;">' . __('Active', 'selective_synchronization') . '</span>';
            } elseif (get_option('edd_' . $plugin_slug . '_license_status') == 'invalid') {
                $license_status = '<span style="color:red;">' . __('Invalid Key', 'selective_synchronization') . '</span>';
            } else {
                $license_status = '<span style="color:red;">' . __('Not Active ', 'selective_synchronization') . '</span>';
            }

            return $license_status;
        }
        /*code ends here */
        public function statusUpdate($license_data)
        {
            $status = "";
            if ((empty($license_data->success)) && ($license_data->error == "expired")) {
                $status = 'expired';
            } elseif ($license_data->license == 'invalid' && $license_data->error == "revoked") {
                $status = 'disabled';
            } elseif ($license_data->license == 'invalid' && $license_data->activations_left == "0") {
                include_once(plugin_dir_path(__FILE__) . 'class-eb-select-get-plugin-data.php');

                $active_site = EBSelectGetPluginData::getSiteList($this->plugin_slug);
                if (!empty($active_site) || $active_site != "") {
                    $status = "invalid";
                } else {
                    $status = 'valid';
                }
            } elseif ($license_data->license == 'failed') {
                $status = 'deactivated';
            } else {
                $status = $license_data->license;
            }
            update_option('edd_' . $this->plugin_slug . '_license_status', $status);
        }

        public function addData()
        {


            if (isset($_POST['edd_' . $this->plugin_slug . '_license_activate'])) {
                if (!check_admin_referer('edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce')) {
                    return;
                }

                $license_key = trim($_POST['edd_' . $this->plugin_slug . '_license_key']);

                if ($license_key) {
                    $api_params = array(
                        'edd_action' => 'activate_license',
                        'license' => $license_key,
                        'item_name' => urlencode($this->plugin_name)
                    );

                    $response = wp_remote_get(add_query_arg($api_params, $this->store_url), array(
                        'timeout' => 15, 'sslverify' => false));

                    if (is_wp_error($response)) {
                        return false;
                    }

                    $license_data = json_decode(wp_remote_retrieve_body($response));

                    $exp_time = strtotime($license_data->expires);
                    $cur_time = time();

                    if ($exp_time <= $cur_time && $exp_time != 0) {
                        $license_data->error = "expired";
                    }


                    if (!empty($license_data->renew_link) /*|| $license_data->renew_link != ""*/) {
                        update_option('eb_' . $this->plugin_slug . '_product_site', $license_data->renew_link);
                    }

                    if (!empty($license_data->sites) /*|| $license_data->sites != ""*/) {
                    } {
                        update_option('eb_' .
                                $this->plugin_slug . '_license_key_sites', $license_data->sites);
                        update_option('eb_' . $this->plugin_slug . '_license_max_site', $license_data->license_limit);
                    }

                    $this->statusUpdate($license_data);

                    update_option('edd_' . $this->plugin_slug . '_license_key', $license_key);

                    $trans_var = get_transient('eb_' . $this->plugin_slug);
                    if (isset($trans_var)) {
                        delete_transient('eb_' . $this->plugin_slug);
                    }
                }
            } else if (isset($_POST['edd_' . $this->plugin_slug . '_license_deactivate'])) {
                if (!check_admin_referer('edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce')) {
                    return;
                }

                $wpep_license_key = trim(get_option('edd_' . $this->plugin_slug . '_license_key'));

                if ($wpep_license_key) {
                    $api_params = array(
                        'edd_action' => 'deactivate_license',
                        'license' => $wpep_license_key,
                        'item_name' => urlencode($this->plugin_name)
                    );

                    $response = wp_remote_get(add_query_arg($api_params, $this->store_url), array(
                        'timeout' => 15, 'sslverify' => false));

                    if (is_wp_error($response)) {
                        return false;
                    }

                    $license_data = json_decode(wp_remote_retrieve_body($response));

                    if ($license_data->license == 'deactivated' || $license_data->license == 'failed') {
                        update_option('edd_' . $this->plugin_slug . '_license_status', 'deactivated');
                    }
                    delete_transient('eb_' . $this->plugin_slug);
                    set_transient('eb_' . $this->plugin_slug, $license_data->license, 0);
                }
            }
        }
    }

}
