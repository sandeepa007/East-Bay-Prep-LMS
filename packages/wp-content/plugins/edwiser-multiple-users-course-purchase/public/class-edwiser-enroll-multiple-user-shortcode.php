<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('EdwiserEnrollMultipleUserShortcode')) {

    class EdwiserEnrollMultipleUserShortcode
    {

        /**
         * Init shortcodes.
         */
        public static function init()
        {
            $shortcodes = array(
                'bridge_woo_enroll_users' => __CLASS__ . '::enrollUsers',
            );

            foreach ($shortcodes as $shortcode => $function) {
                add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
            }
        }

        /**
         * Shortcode Wrapper.
         *
         * @since  1.0.0
         *
         * @param mixed $function
         * @param array $atts     (default: array())
         *
         * @return string
         */
        public static function shortcodeWrapper(
            $function,
            $atts = array(),
            $wrapper = array('class' => '', 'before' => null, 'after' => null)
        ) {
            ob_start();

            $before = empty($wrapper['before']) ? '<div class="' . esc_attr($wrapper['class']) . '">' : $wrapper['before'];
            $after = empty($wrapper['after']) ? '</div>' : $wrapper['after'];

            echo $before;
            call_user_func($function, $atts);
            echo $after;

            return ob_get_clean();
        }

        /**
         * enroll user shortcode.
         *
         * @since  1.0.0
         *
         * @param mixed $atts
         *
         * @return string
         */
        public static function enrollUsers($atts)
        {
            return self::shortcodeWrapper(
                array(
                    'app\wisdmlabs\edwiserBridge\BulkPurchase\EBShortCodeEnrollUsers',
                     'output'
                    ),
                $atts
            );
        }
    }
}
