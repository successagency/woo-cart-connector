<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Cart Connector
 * Plugin URI:        https://wearelucyd.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Lucyd
 * Author URI:        https://wearelucyd.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'WOO_CONNECTOR_VERSION', '1.0.0' );
define( 'WOO_CONNECTOR_FILE', __FILE__ ); // this file
define( 'WOO_CONNECTOR_BASENAME', plugin_basename( WOO_CONNECTOR_FILE ) ); // plugin name as known by WP
define( 'WOO_CONNECTOR_DIR', dirname( WOO_CONNECTOR_FILE ) ); // our directory

// include_once 'vendor/autoload.php';

$controllers = [
    'includes/Checkout.php',
];

foreach ($controllers as $file) {
    include_once $file;
}
