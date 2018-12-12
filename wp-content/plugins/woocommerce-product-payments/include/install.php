<?php


class Woocommerce_Product_Payment_Install {

	protected $loader;

	protected $plugin_slug;

	protected $version;

	public function __construct($softsdev_pd_payments_file)
	{
		if (!isset($softsdev_pd_payments_file)) {
			$softsdev_pd_payments_file = dirname( __FILE__ ) . '../woocommerce-payment-gateway-per-product.php';
		}
		$this->softsdev_pd_payments_file = $softsdev_pd_payments_file;
		$this->plugin_slug = 'woocommerce-product-payment-slug';
		$this->version = '2.5.5';

		$this->load_dependencies();

	}

	private function load_dependencies()
	{
		require_once plugin_dir_path( __FILE__ ) . 'loader.php';
		$this->loader = new Woocommerce_Product_Payment_Loader();
		$this->define_hooks();
	}

	private function define_hooks()
	{
		
		/**
		 * Check is paid plugin is installed then we not activate this
		 */
		if (is_plugin_active('woocommerce-product-payments-premium/woocommerce-product-payments-premium.php')) {
		    
		    $this->loader->add_action('admin_notices', 'softsdev_wpp_activation_notice');
		    deactivate_plugins($this->softsdev_pd_payments_file);

		} elseif (is_plugin_active('woocommerce/woocommerce.php')) {
			
			function softsdev_product_payments_enqueue() {
		        wp_enqueue_style('softsdev_pd_payments_enqueue', plugin_dir_url(__FILE__) . '../css/style.css');
		    }

		    function softdev_product_payments_submenu_page() {
		        add_submenu_page('woocommerce', __('Product Payments', 'softsdev'), __('Product Payments', 'softsdev'), 'manage_options', 'softsdev-product-payments', 'dfm_wcpgpp_product_payments_settings');
		    }

			$this->loader->add_action('admin_enqueue_scripts', null, 'softsdev_product_payments_enqueue');
		    $this->loader->add_action('admin_menu', null, 'softdev_product_payments_submenu_page');
		    
		    add_action('save_post', 'wpp_meta_box_save', 10, 2);
		    
		    $this->loader->add_action('add_meta_boxes', null, 'wpp_meta_box_add');
		    //$this->loader->add_action('admin_menu', null, 'wppg_menu');
			$this->loader->add_action("admin_enqueue_scripts", null, "wppg_admin_scripts");

			$this->loader->add_filter('woocommerce_available_payment_gateways', null, 'wpppayment_gateway_disable_country');

		}

	}

	public function run() {
		$this->loader->run();
	}

	public function get_version() {
		return $this->version;
	}

}





