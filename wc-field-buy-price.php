<?php
/*
Plugin Name: WooCommerce field buy price
Plugin URI: https://github.com/Ohar/wc-field-buy-price
Description:  Add custom field “buy_price” to the WooCommerce products
Author: Pavel Lysenko aka Ohar
Author URI: http://ohar.name/
Contributors: ohar
Version: 1.0.1
License: MIT
Text Domain: wc-field-buy-price
Domain Path: /languages
*/

// Inspired by http://www.remicorson.com/mastering-woocommerce-products-custom-fields/

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_woocommerce_general_field_buy_price' );

// Save Fields
add_action( 'woocommerce_process_product_meta', 'save_custom_woocommerce_general_field_buy_price' );

function add_custom_woocommerce_general_field_buy_price() {

  global $woocommerce, $post;

  echo '<div class="options_group">';

	woocommerce_wp_text_input(
		array(
			'id'                => 'buy_price',
			'label'             => __( 'Buy Price', 'wc-field-buy-price' ),
			'placeholder'       => '0',
			'desc_tip'          => 'true',
			'description'       => __( 'Price of buying product from vendor', 'wc-field-buy-price' ),
			'type'              => 'number',
			'custom_attributes' => array(
				'step' 	=> 'any',
				'min'	  => '0'
			)
)
	);

	echo '</div>';
}

function save_custom_woocommerce_general_field_buy_price( $post_id ) {
	$buy_price = $_POST['buy_price'];
	if (!empty( $buy_price ) ) {
		update_post_meta( $post_id, 'buy_price', esc_attr( $buy_price ) );
	}
}

add_action( 'plugins_loaded', 'load_wc_field_buy_price_textdomain' );

function load_wc_field_buy_price_textdomain() {
	load_plugin_textdomain( 'wc-field-buy-price', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
