<?php

namespace ebSelectSync\includes;

if (!class_exists('EBSelectGetPluginData')) {
    class EBSelectGetPluginData
    {

        public static function getDataFromDb($plugin_data)
        {

           // $author_name = $plugin_data['author_name'];
            $plugin_name = $plugin_data['plugin_name'];
            //$plugin_short_name = $plugin_data['plugin_short_name'];
            $plugin_slug = $plugin_data['plugin_slug'];
            //$plugin_version = $plugin_data['plugin_version'];
            $store_url = $plugin_data['store_url'];

            $get_trans = get_transient('eb_' . $plugin_slug);

            if (!$get_trans) {
                $license_key = trim(get_option('edd_' . $plugin_slug . '_license_key'));
                
                if ($license_key) {
                    $api_params = array(
                        'edd_action' => 'check_license',
                        'license' => $license_key,
                        'item_name' => urlencode($plugin_name)
                    );

                    $response = wp_remote_get(add_query_arg($api_params, $store_url), array(
                        'timeout' => 15, 'sslverify' => false));

                    if (is_wp_error($response)) {
                        return false;
                    }

                    $license_data = json_decode(wp_remote_retrieve_body($response));

                    $active_site = EBSelectGetPluginData::getSiteList($plugin_data['plugin_slug']);

                    update_option('edd_' . $plugin_slug . '_license_status', $license_data->license);
                    
                    $license_status = $license_data->license;
                    
                    if ($license_status == 'valid') {
                        set_transient('eb_' . $plugin_slug, $license_data->license, 60 * 60 * 24 * 7);
                        return 'available';
                    } elseif ($license_status == 'expired' && (!empty($active_site) || $active_site != "")) {
                        set_transient('eb_' . $plugin_slug, $license_data->license, 60 * 60 * 5);
                        return 'unavailable';
                    } elseif ($license_status == 'expired') {
                        set_transient('eb_' . $plugin_slug, $license_data->license, 60 * 60 * 24 * 7);
                        return 'available';
                    } else {
                        set_transient('eb_' . $plugin_slug, $license_data->license, 60 * 60 * 5);
                    }
                    return 'unavailable';
                }
            } else {
                $license_status = get_option('edd_' . $plugin_slug . '_license_status');
                $active_site = EBSelectGetPluginData::getSiteList($plugin_data['plugin_slug']);

                if ($license_status == 'valid') {
                    return 'available';
                } elseif ($license_status == 'expired' && (!empty($active_site) || $active_site != "")) {
                    return 'unavailable';
                } elseif ($license_status == 'expired') {
                    return 'available';
                } else {
                    return 'unavailable';
                }
            }
        }

        /**
         * This function is used to get list of sites where license key is already acvtivated.
         *
         * @param type $plugin_slug current plugin's slug
         * @return string  list of site
         *
         * @author Foram Rambhiya
         *
         */
        
        
        public static function getSiteList($plugin_slug)
        {
            $sites = get_option('eb_' . $plugin_slug . '_license_key_sites');
            $max=get_option('eb_' . $plugin_slug .'_license_max_site');
            $cur_site = get_site_url();
            $cur_site = preg_replace('#^https?://#', '', $cur_site);
            
            $site_count = 0;
            $active_site = "";

            if (!empty($sites) || $sites != "") {
                foreach ($sites as $key) {
                    foreach ($key as $value) {
                        $value = rtrim($value, "/");

                        if (strcasecmp($value, $cur_site) != 0) {
                            $active_site.= "<li>" . $value . "</li>";
                            $site_count++;
                        }
                    }
                }
            }

            if ($site_count>=$max) {
                return $active_site;
            } else {
                return "";
            }
        }
    }

}
