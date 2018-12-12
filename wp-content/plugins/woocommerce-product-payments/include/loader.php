<?php

class Woocommerce_Product_Payment_Loader {

	protected $actions;

	protected $filters;

	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	public function add_action( $hook, $component, $callback, $priority=null, $args=null ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback );
	}

	public function add_filter( $hook, $component, $callback ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback );
	}

	private function add( $hooks, $hook, $component, $callback ) {

		$hooks[] = array(
			'hook'      => $hook,
			'component' => $component,
			'callback'  => $callback
		);

		return $hooks;

	}

	public function run() {

		 foreach ( $this->filters as $hook ) {
		 	if (!empty($hook['component'])) {
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
			} else {
				add_filter( $hook['hook'], $hook['callback']);
			}
		 }

		 foreach ( $this->actions as $hook ) {
			if (!empty($hook['component'])) {
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
			} else {
				add_action( $hook['hook'], $hook['callback'] );
			}
		 }

	}

}