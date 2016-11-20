<?php
/*
Plugin Name: WooCommerce field buy price
Plugin URI: https://github.com/Ohar/wc-field-buy-price
Description:  Add custom field “buy_price” to the WooCommerce products
Author: Pavel Lysenko aka Ohar
Author URI: http://ohar.name/
Contributors: ohar
Version: 1.0.4
License: MIT
Text Domain: wc-field-buy-price
Domain Path: /languages
*/

// Inspired by 
// 1. http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
// 2. http://stackoverflow.com/questions/27262032/add-custom-product-field-on-quick-edit-option-on-the-product-listing-of-a-woocom




// PRODUCT PAGE

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_woocommerce_general_field_buy_price' );

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

// Save Fields
add_action( 'woocommerce_process_product_meta', 'save_custom_woocommerce_general_field_buy_price' );

function save_custom_woocommerce_general_field_buy_price( $post_id ) {
	update_post_meta( $post_id, 'buy_price', esc_attr( $_POST['buy_price'] ) );
}




// L10N

add_action( 'plugins_loaded', 'load_wc_field_buy_price_textdomain' );

function load_wc_field_buy_price_textdomain() {
	load_plugin_textdomain( 'wc-field-buy-price', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}




// QUICK EDIT

// Add field to the quick edit
add_action( 'woocommerce_product_quick_edit_start', 'product_quick_edit_add_field_buy_price' );

function product_quick_edit_add_field_buy_price() { ?>
		<div class="buy_price_block">
			<label class="alignleft">
					<span class="title"><?php _e('Buy Price', 'wc-field-buy-price' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="buy_price" class="text buy_price" placeholder="<?php _e( 'Price of buying product from vendor', 'wc-field-buy-price' ); ?>" value="">
					</span>
			</label>
		</div>
    <br class="clear">
		
		<script>
		
		jQuery(function(){
			jQuery('#the-list').on('click', '.editinline', function(){

					/**
					 * Extract metadata and put it as the value for the custom field form
					 */
					inlineEditPost.revert();

					var post_id = jQuery(this).closest('tr').attr('id');

					post_id = post_id.replace("post-", "");

					var $field_inline_data = jQuery('#buy_price_inline_' + post_id),
							$wc_inline_data    = jQuery('#woocommerce_inline_' + post_id );

					jQuery('input[name="buy_price"]', '.inline-edit-row').val($field_inline_data.find("#buy_price").text());


					/**
					 * Only show custom field for appropriate types of products (simple)
					 */
					var product_type = $wc_inline_data.find('.product_type').text();

					if (product_type=='simple') {
							jQuery('.buy_price_block', '.inline-edit-row').show();
					} else {
							jQuery('.buy_price_block', '.inline-edit-row').hide();
					}

			});
		});
		
		</script>
		
    <?php
}

// Save field to the quick edit
add_action( 'woocommerce_product_quick_edit_save', 'product_quick_edit_save_field_buy_price', 10, 1);

function product_quick_edit_save_field_buy_price($product) {
	if ($product->is_type('simple')) {
		$post_id = $product->id;
		if ( isset( $_REQUEST['buy_price'] ) ) {
			$customFieldDemo = trim(esc_attr( $_REQUEST['buy_price'] ));
			update_post_meta( $post_id, 'buy_price', wc_clean( $customFieldDemo ) );
		}
	}
}


// Show field data at the quick edit
add_action( 'manage_product_posts_custom_column', 'product_quick_edit_show_field_buy_price', 99, 2);

function product_quick_edit_show_field_buy_price($column,$post_id) {
	switch ( $column ) {
		case 'name' : ?>
				<div class="hidden buy_price_inline" id="buy_price_inline_<?php echo $post_id; ?>">
						<div id="buy_price"><?php echo get_post_meta($post_id, 'buy_price', true); ?></div>
				</div>
				<?php

				break;

		default :
				break;
	}
}
