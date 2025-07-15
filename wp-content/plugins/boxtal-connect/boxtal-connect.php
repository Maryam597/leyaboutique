<?php
/**
 * Plugin Name: Boxtal Connect
 * Description: Negotiated rates for all types of shipping (home, relay, express, etc.). No subscription, no hidden fees.
 * Author: Boxtal
 * Author URI: https://www.boxtal.com
 * Text Domain: boxtal-connect
 * Domain Path: /Boxtal/BoxtalConnectWoocommerce/translation
 * Version: 1.3.5
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 2.6.14
 * WC tested up to: 9.8.2
 *
 * @package Boxtal\BoxtalConnectWoocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

require_once trailingslashit( __DIR__ ) . 'Boxtal/BoxtalConnectWoocommerce/autoloader.php';

use Boxtal\BoxtalConnectWoocommerce\Plugin;

$plugin_instance = Plugin::initInstance( __FILE__ );

add_action( 'before_woocommerce_init', array( $plugin_instance, 'plugins_before_woocommerce_init_action' ) );

add_action( 'plugins_loaded', array( $plugin_instance, 'plugins_loaded_action' ) );

add_action( 'wpmu_new_blog', array( $plugin_instance, 'wpmu_new_blog_action' ), 10, 6 );

add_action( 'wpmu_drop_tables', array( $plugin_instance, 'wpmu_drop_tables_action' ) );

register_activation_hook( __FILE__, 'Boxtal\BoxtalConnectWoocommerce\Plugin::activation_hook' );

register_uninstall_hook( __FILE__, 'Boxtal\BoxtalConnectWoocommerce\Plugin::uninstall_hook' );

