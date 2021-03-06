<?
/*
Plugin Name: WooCommerce field buy price
Plugin URI: https://github.com/Ohar/wc-field-buy-price
Description:  Add custom field “buy_price” to the WooCommerce products
Author: Pavel Lysenko aka Ohar
Author URI: http://ohar.name/
Contributors: ohar
Version: 1.1.1
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




// BULK EDIT

// Add field to the bulk edit
add_action( 'woocommerce_product_bulk_edit_start', 'product_bulk_edit_add_field_buy_price' );

function product_bulk_edit_add_field_buy_price() { ?>
	<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><? _e( 'Buy Price', 'wc-field-buy-price' ); ?></span>
				<span class="input-text-wrap">
					<select class="change_buy_price change_to" name="change_buy_price">
					<?
						$options = array(
							'' 	=> __( '— No change —', 'woocommerce' ),
							'1' => __( 'Change to:', 'woocommerce' ),
							'2' => __( 'Increase by (fixed amount or %):', 'woocommerce' ),
							'3' => __( 'Decrease by (fixed amount or %):', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . $value . '</option>';
						}
					?>
					</select>
				</span>
			</label>
			<label class="change-input">
				<input type="text" name="buy_price" class="text buy_price" placeholder="<?=__( 'Price of buying product from vendor', 'wc-field-buy-price')?>" value="" />
				<? echo get_woocommerce_currency_symbol(); ?>
			</label>
	</div>
	
	<style>
	 #woocommerce-fields-bulk.inline-edit-col .buy_price {
    box-sizing: border-box;
    width: calc(100% - 1rem);
    margin-left: 0;
	}
	</style>
	<?
}

// Save field to the bulk edit
add_action( 'woocommerce_product_bulk_edit_save', 'product_bulk_edit_save_field_buy_price', 10, 1);

function product_bulk_edit_save_field_buy_price($product) {
	if ($product->is_type('simple')) {
		$post_id = $product->id;
		
		if ( isset( $_REQUEST['buy_price'] ) &&  isset( $_REQUEST['change_buy_price'] )) {
			$buy_price_input = trim(esc_attr( $_REQUEST['buy_price'] ));
			$change_buy_price = trim(esc_attr( $_REQUEST['change_buy_price'] ));
			$old_buy_price = get_post_meta($post_id, 'buy_price', true);
			
			switch ( $change_buy_price ) {
				case '1' :
						$new_buy_price = $buy_price_input;
						update_post_meta( $post_id, 'buy_price', wc_clean( $new_buy_price ) );
						break;
				case '2' :
						if (strpos($buy_price_input, '%') === false) {
							$new_buy_price = $old_buy_price + $buy_price_input;
						} else {
							$num = get_numerics($buy_price_input);
							$new_buy_price = $old_buy_price * (1 + $num / 100);
						}
						update_post_meta( $post_id, 'buy_price', wc_clean( $new_buy_price ) );
						break;
						
				case '3' :
						if (strpos($buy_price_input, '%') === false) {
							$new_buy_price = $old_buy_price - $buy_price_input;
						} else {
							$num = get_numerics($buy_price_input);
							$new_buy_price = $old_buy_price * (1 - $num / 100);
						}
						update_post_meta( $post_id, 'buy_price', wc_clean( $new_buy_price ) );
						break;

				default :
						break;
			}
		}
	}
}

function get_numerics ($str) {
    preg_match_all('/\d+/', $str, $matches);
    return implode('', $matches[0]);
}


// QUICK EDIT

// Add field to the quick edit
add_action( 'woocommerce_product_quick_edit_start', 'product_quick_edit_add_field_buy_price' );

function product_quick_edit_add_field_buy_price() { ?>
		<div class="buy_price_block">
			<label class="alignleft">
					<span class="title"><? _e('Buy Price', 'wc-field-buy-price' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="buy_price" class="text buy_price" placeholder="<? _e( 'Price of buying product from vendor', 'wc-field-buy-price' ); ?>" value="">
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
		
    <?
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
				<div class="hidden buy_price_inline" id="buy_price_inline_<? echo $post_id; ?>">
						<div id="buy_price"><? echo get_post_meta($post_id, 'buy_price', true); ?></div>
				</div>
				<?

				break;

		default :
				break;
	}
}
