<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

use app\wisdmlabs\edwiserBridge as edwiserBridge;

/**
 * The file that defines the enroll user shortcode.
 *
 * @link  https://edwiser.org
 * @since 1.0.2
 *
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
if (!class_exists('EBShortCodeEnrollUsers')) {
    class EBShortCodeEnrollUsers
    {
        /**
         * Get the shortcode content.
         *
         * @since  1.0.0
         *
         * @param array $atts
         *
         * @return string
         */
        public static function get($atts)
        {
            return \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserEnrollMultipleUserShortcode::shortcodeWrapper(
                array(__CLASS__, 'output'),
                $atts
            );
        }

        /**
         * Output the shortcode.
         *
         * @since  1.0.0
         *
         * @param array $atts
         */
        public static function output($atts)
        {
            extract(
                shortcode_atts(
                    array(
                    'user_id' => '',
                    ),
                    $atts
                )
            );

            $plu_template_loader = new edwiserBridge\EbTemplateLoader(
                edwiserBridge\edwiserBridgeInstance()->getPluginName(),
                edwiserBridge\edwiserBridgeInstance()->getVersion()
            );
            $user_ID = get_current_user_id();
            $plu_template_loader->wpGetTemplate(
                'enroll-users-page.php',
                array(
                'user_id' => $user_ID,
                    ),
                '',
                EB_WOO_EU_PLUGIN_DIR.'public/templates/'
            );
        }
    }
}
