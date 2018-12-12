<?php

/**
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        self::paypal_shopping_cart_for_wordPress_add_shortcode();
        add_filter('widget_text', 'do_shortcode');
    }

    public static function paypal_shopping_cart_for_wordPress_add_shortcode() {
        add_shortcode('paypal_ipn_list', array(__CLASS__, 'paypal_ipn_for_wordpress_paypal_ipn_list'));
        add_shortcode('paypal_ipn_data', array(__CLASS__, 'paypal_ipn_for_wordpress_paypal_ipn_paypal_ipn_data'));
    }

    public static function paypal_ipn_for_wordpress_paypal_ipn_paypal_ipn_data($atts) {

        extract(shortcode_atts(array(
            'txn_id' => 'txn_id',
            'field' => 'first_name',
                        ), $atts));


        ob_start();

        if (isset($atts['txn_id']) && !empty($atts['txn_id'])) {
            $args = array(
                'post_type' => 'paypal_ipn',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => 'txn_id',
                        'value' => $atts['txn_id'],
                        'compare' => 'LIKE'
                    )
                )
            );

            $posts = get_posts($args);
            if (isset($posts[0]->ID) && !empty($posts[0]->ID)) {
                return get_post_meta($posts[0]->ID, $field, true);
                return ob_get_clean();
            } else {
                $mainhtml = "no records";
                return ob_get_clean();
            }
        } else {
            $mainhtml = "transaction id not found.";
            return ob_get_clean();
        }
    }

    public static function paypal_ipn_for_wordpress_paypal_ipn_list($atts) {

        extract(shortcode_atts(array(
            'txn_type' => 'any',
            'payment_status' => '',
            'limit' => 10,
            'field1' => 'txn_id',
            'field2' => 'payment_date',
            'field3' => 'first_name',
            'field4' => 'last_name',
            'field5' => 'mc_gross',
                        ), $atts));


        ob_start();


        if (empty($payment_status)) {
            $paypal_ipn_type = get_terms('paypal_ipn_type');
            $term_ids = wp_list_pluck($paypal_ipn_type, 'slug');
        } else {
            $term_ids = array('0' => $payment_status);
        }

        $args = array(
            'post_type' => 'paypal_ipn',
            'post_status' => $txn_type,
            'posts_per_page' => $limit,
            'tax_query' => array(
                array(
                    'taxonomy' => 'paypal_ipn_type',
                    'terms' => array_map('sanitize_title', $term_ids),
                    'field' => 'slug'
                )
            )
        );

        if (isset($atts) && !empty($atts)) {
            $start_loop = 1;
            $field_key_header = array();
            $field_key = array();
            foreach ($atts as $atts_key => $atts_value) {
                if (array_key_exists('field' . $start_loop, $atts)) {
                    $field_key_header['field' . $start_loop] = ucwords(str_replace('_', ' ', $atts['field' . $start_loop]));
                    $field_key['field' . $start_loop] = $atts['field' . $start_loop];
                }
                $start_loop = $start_loop + 1;
            }
        }

        $posts = get_posts($args);
        if ($posts) {
            
            $mainhtml = '';
            $output = '';
            $output .= '<table id="example" class="display" cellspacing="0" width="100%"><thead>';

            $thead = "<tr>";

            if(!empty($field_key_header))
            {
                foreach ($field_key_header as $field_key_header_key => $field_key_header_value) {
                    $thead .= "<th>" . $field_key_header_value . "</th>";
                }
            }

            $thead .= "</tr>";


            $thead_end = '</thead>';
            $tfoot_start = "<tfoot>";
            $tfoot_end = "</tfoot>";
            $mainhtml .= $output . $thead . $thead_end . $tfoot_start . $thead . $tfoot_end;
            $tbody_start = "<tbody>";
            $tbody = "";
            foreach ($posts as $post):
                $tbody .= "<tr>";
                $mc_currency = get_post_meta($post->ID, 'mc_currency', true);
                $currency_symbol = self::get_currency_symbol($mc_currency);
                if (isset($field_key) && !empty($field_key)) {
                    foreach ($field_key as $field_key_key => $field_key_value) {
                        if($field_key_value == 'mc_gross') {
                            $tbody .= "<td>" . $currency_symbol . get_post_meta($post->ID, $field_key_value, true) . "</td>";
                        } else {
                            $tbody .= "<td>" . get_post_meta($post->ID, $field_key_value, true) . "</td>";
                        }
                    }
                }

                $tbody .= "</tr>";
            endforeach;

            $tbody_end = "</tbody>";
            $mainhtml .= $tbody_start . $tbody . $tbody_end;
            $mainhtml .= "</table>";
            return $mainhtml;
            return ob_get_clean();
        } else {
            $mainhtml = "no records found";
            return ob_get_clean();
        }
    }
    
    public static function get_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = 'USD';
	}

	$symbols = array(
		'AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => 'Afl.',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BYN' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '&#8377;',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
		'ISK' => 'kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x441;&#x43e;&#x43c;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'MDL',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'CFA',
		'XCD' => '&#36;',
		'XOF' => 'CFA',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
	);
	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return $currency_symbol;
}


}

AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display::init();
