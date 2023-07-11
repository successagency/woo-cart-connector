<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


/**
 * WC Product Add to Cart by SKU class
 */
class WC_Copy_Cart
{

	const ALIAS_DOMAIN_HOSTNAME = 'drinkbrez.xyz';
	const ALIAS_DOMAIN_URL = 'https://drinkbrez.xyz';
	const PRIMARY_DOMAIN_URL = 'https://testenviron670.wpengine.com';

	/**
	 * Constructor
	 */
	public function __construct()
	{

		add_action('wp_enqueue_scripts', array($this, 'enqueue_front_scripts'));
		add_filter('template_redirect', array($this, 'maybe_copy_cart'), 10);
		add_filter('wp_head', array($this, 'add_vars'), 10);
	}

	function add_vars(){
		?>
		<script>
			var wccPrimaryDomainHostname = '<?= self::ALIAS_DOMAIN_HOSTNAME ?>';
			var wccPrimaryDomainUrl = '<?= self::PRIMARY_DOMAIN_URL ?>';
			var wccAliasDomainUrl = '<?= self::ALIAS_DOMAIN_URL ?>';
		</script>
		<?php
	}

	function maybe_copy_cart()
	{

		if (isset($_GET['token'])) {

			WC()->cart->empty_cart();

			$token = $_GET['token'];
			$cart = $this->get_cart_by_token($token);

			if (empty($cart->items)) {
				return;
			}

			foreach ($cart->items as $item) {

				$product_id = $item->id;
				$qty = $item->quantity;
				$permalink_qs = parse_url($item->permalink, PHP_URL_QUERY);
				parse_str($permalink_qs, $meta_args);
				$meta = [];

				## Check for subscriptions
				if (!empty($meta_args)) {
					if (class_exists('WCS_ATT_Product_Schemes') && isset($meta_args["convert_to_sub_{$product_id}"])) {
						$posted_subscription_scheme_option = wc_clean($meta_args["convert_to_sub_{$product_id}"]);
						$posted_subscription_scheme_key    = WCS_ATT_Product_Schemes::parse_subscription_scheme_key($posted_subscription_scheme_option);
						$meta['wcsatt_data'] = array(
							'active_subscription_scheme' => $posted_subscription_scheme_key,
						);
					}
				}

				## Add to cart
				try {
					$check_variable = wp_get_post_parent_id($product_id);
					// Simple products
					if ($check_variable == 0) {
						WC()->cart->add_to_cart(
							$product_id,
							$qty,
							0,
							array(),
							$meta
						);
					}
					// Variable Products
					else {
						$variation_id = $check_variable;
						WC()->cart->add_to_cart(
							$product_id,
							$qty,
							$variation_id,
							array(),
							$meta
						);
					}
				} catch (Exception $e) {
					return new WP_Error('add_to_cart_error', $e->getMessage(), array('status' => 400));
				}

				unset($product_id);
				unset($qty);
				unset($variation_id);
				unset($product);
				unset($permalink_qs);
				unset($meta_args);
				unset($meta);
			}

			// Coupons
			foreach ($cart->coupons as $coupon) {
				WC()->cart->apply_coupon($coupon->code);
			}

			wp_redirect(wc_get_checkout_url());
			exit;
		}
		
	}

	public static function get_cart_by_token($token)
	{

		$url = self::PRIMARY_DOMAIN_URL . "/wp-json/wc/store/v1/cart";

		$arr = array(
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(
				'Accept' => 'application/json',
				'Cart-Token' => $token,
			),
			'cookies'     => array()
		);

		$response = wp_remote_get($url, $arr);

		// error check
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			return false;
		} else {
			$headers = wp_remote_retrieve_headers($response);
			$body = wp_remote_retrieve_body($response);
			return json_decode($body);
		}
	}

	function enqueue_front_scripts()
	{
		wp_enqueue_script('frontend', plugin_dir_url(dirname(__FILE__)) . 'assets/js/frontend.js', [], filemtime(plugin_dir_url(dirname(__FILE__)) . 'assets/js/frontend.js'), true);
	}
}

new WC_Copy_Cart();
