<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

// uncomment this line for testing
//set_site_transient( 'update_plugins', null );

/**
 * Allows plugins to use their own update API.
 *
 * @author Pippin Williamson
 * @modified WisdmLabs
 *
 * @version 1.1
 */
if (!class_exists('EBMUSelectPluginUpdater')) {
    class EBMUSelectPluginUpdater
    {
        private $apiUrl = '';
        private $apiData = array();
        private $name = '';
        private $slug = '';
        private static $responseData;

        /**
         * Class constructor.
         *
         * @uses plugin_basename()
         * @uses hook()
         *
         * @param string $_api_url     The URL pointing to the custom API endpoint.
         * @param string $_plugin_file Path to the plugin file.
         * @param array  $_api_data    Optional data to send with API calls.
         */
        public function __construct($apiUrl, $pluginFile, $apiData = null)
        {
            $this->apiUrl  = trailingslashit($apiUrl);
            $this->apiData = urlencode_deep($apiData);
            $this->name     = plugin_basename($pluginFile);
            $this->slug     = basename($pluginFile, '.php');
            $this->version  = $apiData['version'];

            // Set up hooks.
            $this->hook();
        }

        /**
         * Set up Wordpress filters to hook into WP's update process.
         *
         * @uses add_filter()
         */
        private function hook()
        {
            // add_action('upgrader_process_complete', array($this, 'updateWordpressDatabase'));
            add_filter('pre_set_site_transient_update_plugins', array(
                $this,
                'preSetSiteTransientUpdatePluginsFilter',
            ));
            add_filter('pre_set_transient_update_plugins', array(
                $this,
                'preSetSiteTransientUpdatePluginsFilter',
            ));
            add_filter('plugins_api', array($this, 'pluginsApiFilter'), 10, 3);
        }

        /**
         * Check for Updates at the defined API endpoint and modify the update array.
         *
         * This function dives into the update api just when Wordpress creates its update array,
         * then adds a custom API call and injects the custom plugin data retrieved from the API.
         * It is reassembled from parts of the native Wordpress plugin update code.
         * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
         *
         * @uses api_request()
         *
         * @param array $_transient_data Update array build by Wordpress.
         *
         * @return array Modified update array with custom plugin data.
         */
        public function preSetSiteTransientUpdatePluginsFilter($_transient_data)
        {
            if (empty($_transient_data)) {
                return $_transient_data;
            }

            $to_send = array('slug' => $this->slug);

            $api_response = $this->apiRequest($to_send);

            if (false !== $api_response && is_object($api_response) && isset($api_response->new_version)) {
                if (version_compare($this->version, $api_response->new_version, '<')) {
                    $_transient_data->response[$this->name] = $api_response;
                }
            }

            return $_transient_data;
        }

        /**
         * Updates information on the "View version x.x details" page with custom data.
         *
         * @uses api_request()
         *
         * @param mixed  $_data
         * @param string $_action
         * @param object $_args
         *
         * @return object $_data
         */
        public function pluginsApiFilter($data, $action = '', $args = null)
        {
            if (( 'plugin_information' != $action ) || ! isset($args->slug) || ( $args->slug != $this->slug )) {
                return $data;
            }

            $toSend = array( 'slug' => $this->slug );

            $apiResponse = $this->apiRequest($toSend);
            if (false !== $apiResponse) {
                $data        = $apiResponse;
            }

            return $data;
        }

        /**
         * Calls the API and, if successfull, returns the object delivered by the API.
         *
         * @uses get_bloginfo()
         * @uses wp_remote_post()
         * @uses is_wp_error()
         *
         * @param string $_action The requested action.
         * @param array  $_data   Parameters for the API action.
         *
         * @return false||object
         */
        private function apiRequest($data)
        {
            if (null !== self::$responseData) {
                return self::$responseData;
            }
            $data = array_merge($this->apiData, $data);

            if ($data['slug'] != $this->slug) {
                return;
            }

            if (empty($data['license'])) {
                return;
            }

            $apiParams = array(
                'edd_action' => 'get_version',
                'license'    => $data['license'],
                'name'       => $data['item_name'],
                'slug'       => $this->slug,
                'author'     => $data['author'],
                'current_version' => $this->version
            );
            
            $request    = wp_remote_get(add_query_arg($apiParams, $this->apiUrl), array( 'timeout' => 15, 'sslverify' => false, 'blocking'  => true ));

            if (! is_wp_error($request)) {
                $request           = json_decode(wp_remote_retrieve_body($request));
                if ($request && isset($request->sections)) {
                    $request->sections = maybe_unserialize($request->sections);
                }
                self::$responseData = $request;
                return $request;
            } else {
                self::$responseData = false;
                return false;
            }
        }
    }
}
