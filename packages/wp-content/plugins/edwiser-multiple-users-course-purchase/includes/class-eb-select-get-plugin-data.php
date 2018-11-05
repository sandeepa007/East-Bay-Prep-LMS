<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('EBMUSelectGetPluginData')) {
    class EBMUSelectGetPluginData
    {

        public static $responseData;
        public static function getDataFromDb($pluginData, $cache = true)
        {

            if (null !== self::$responseData && $cache === true) {
                return self::$responseData;
            }
       
            $pluginName = $pluginData[ 'plugin_name' ];
            $pluginSlug = $pluginData[ 'plugin_slug' ];
            $storeUrl   = $pluginData[ 'store_url' ];

            $licenseTransient = get_transient('wdm_' . $pluginSlug . '_license_trans');
            
            if (! $licenseTransient) {
                $licenseKey = trim(get_option('edd_' . $pluginSlug . '_license_key'));

                if ($licenseKey) {
                    $apiParams = array(
                        'edd_action'         => 'check_license',
                        'license'            => $licenseKey,
                        'item_name'          => urlencode($pluginName),
                        'current_version'    => $pluginData[ 'plugin_version' ]
                    );

                    $response = wp_remote_get(add_query_arg($apiParams, $storeUrl), array(
                        'timeout'    => 15, 'sslverify'  => false, 'blocking'    => true ));

                    if (is_wp_error($response)) {
                        return false;
                    }


                    $licenseData = json_decode(wp_remote_retrieve_body($response));

                    $validResponseCode = array( '200', '301' );

                    $currentResponseCode = wp_remote_retrieve_response_code($response);

                    if ($licenseData == null || ! in_array($currentResponseCode, $validResponseCode)) {
                        //if server does not respond, read current license information
                        $licenseStatus = get_option('edd_' . $pluginSlug . '_license_status', '');
                        if (empty($licenseData)) {
                            set_transient('wdm_' . $pluginSlug . '_license_trans', 'server_did_not_respond', 60 * 60 * 24);
                        }
                    } else {
                        include_once(plugin_dir_path(__FILE__) . 'class-eb-select-add-plugin-data-in-db.php');
                        $licenseStatus = EBMUSelectAddPluginDataInDB::updateStatus($licenseData, $pluginSlug);
                    }

                    $activeSite = self::getSiteList($pluginSlug);

                    self::setResponseData($licenseStatus, $activeSite, $pluginSlug, true);

                    return self::$responseData;
                }
            } else {
                $licenseStatus  = get_option('edd_' . $pluginSlug . '_license_status');
                $activeSite     = self::getSiteList($pluginSlug);

                self::setResponseData($licenseStatus, $activeSite, $pluginSlug);
                return self::$responseData;
            }
        }

        /**
         * This function is used to get list of sites where license key is already acvtivated.
         *
         * @param type $plugin_slug current plugin's slug
         *
         * @return string list of site
         *
         * @author Foram Rambhiya
         */
        public static function getSiteList($plugin_slug)
        {
            $sites = get_option('eb_'.$plugin_slug.'_license_key_sites');
            $max = get_option('eb_'.$plugin_slug.'_license_max_site');
            $cur_site = get_site_url();
            $cur_site = preg_replace('#^https?://#', '', $cur_site);

            $site_count = 0;
            $active_site = '';

            if (!empty($sites) || '' != $sites) {
                foreach ($sites as $key) {
                    foreach ($key as $value) {
                        $value = rtrim($value, '/');

                        if (strcasecmp($value, $cur_site) != 0) {
                            $active_site .= '<li>'.$value.'</li>';
                            ++$site_count;
                        }
                    }
                }
            }

            if ($site_count >= $max) {
                return $active_site;
            } else {
                return '';
            }
        }



        public static function setResponseData($licenseStatus, $activeSite, $pluginSlug, $setTransient = false)
        {

            if ($licenseStatus == 'valid') {
                self::$responseData = 'available';
            } elseif ($licenseStatus == 'expired' && ( ! empty($activeSite) || $activeSite != "")) {
                self::$responseData = 'unavailable';
            } elseif ($licenseStatus == 'expired') {
                self::$responseData = 'available';
            } else {
                self::$responseData  = 'unavailable';
            }

            if ($setTransient) {
                if ($licenseStatus == 'valid') {
                    $time = 60 * 60 * 24 * 7;
                } else {
                    $time = 60 * 60 * 24;
                }
                set_transient('wdm_' . $pluginSlug . '_license_trans', $licenseStatus, $time);
            }
        }
    }
}
