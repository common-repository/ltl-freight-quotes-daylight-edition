<?php
/**
 * Plugin Name: LTL Freight Quotes - Daylight Edition
 * Plugin URI: https://eniture.com/products/
 * Description: Dynamically retrieves your negotiated shipping rates from Daylight and displays the results in the WooCommerce shopping cart.
 * Version: 2.2.4
 * Author: Eniture Technology
 * Author URI: http://eniture.com/
 * Text Domain: eniture-technology
 * License: GPL version 2 or later - http://www.eniture.com/
 * WC requires at least: 6.4
 * WC tested up to: 9.1.4
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once 'vendor/autoload.php';

define('EN_DAYLIGHT_MAIN_DIR', __DIR__);
define('EN_DAYLIGHT_MAIN_FILE', __FILE__);

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

if (empty(\EnDaylightGuard\EnDaylightGuard::en_check_prerequisites('Daylight', '5.6', '4.0', '2.3'))) {
    require_once 'en-install.php';
}

/**
 * Load scripts for Daylight Freight json tree view
 */
if (!function_exists('en_daylight_jtv_script')) {
    function en_daylight_jtv_script()
    {
        wp_register_style('json_tree_view_style_daylight', plugin_dir_url(__FILE__) . 'admin/tab/logs/en-json-tree-view/en-jtv-style.css');
        wp_register_script('json_tree_view_script_daylight', plugin_dir_url(__FILE__) . 'admin/tab/logs/en-json-tree-view/en-jtv-script.js', ['jquery'], '1.0.0');
        wp_enqueue_style('json_tree_view_style_daylight');
        wp_enqueue_script('json_tree_view_script_daylight', [
            'en_tree_view_url' => plugins_url(),
        ]);
    }
    
    add_action('admin_init', 'en_daylight_jtv_script');
}

add_filter('en_suppress_parcel_rates_hook', 'supress_parcel_rates');
if (!function_exists('supress_parcel_rates')) {
    function supress_parcel_rates() {
        $exceedWeight = get_option('en_plugins_return_LTL_quotes') == 'yes';
        $supress_parcel_rates = get_option('en_suppress_parcel_rates') == 'suppress_parcel_rates';
        return ($exceedWeight && $supress_parcel_rates);
    }
}

/**
 * Remove Option For Daylight
 */
if (!function_exists('en_daylight_deactivate_plugin')) {
    function en_daylight_deactivate_plugin($network_wide = null)
    {
        if ( is_multisite() && $network_wide ) {
            foreach (get_sites(['fields'=>'ids']) as $blog_id) {
                switch_to_blog($blog_id);
                $eniture_plugins = get_option('EN_Plugins');
                $plugins_array = json_decode($eniture_plugins, true);
                $plugins_array = !empty($plugins_array) && is_array($plugins_array) ? $plugins_array : array();
                $key = array_search('daylight', $plugins_array);
                if ($key !== false) {
                    unset($plugins_array[$key]);
                }
                update_option('EN_Plugins', json_encode($plugins_array));
                restore_current_blog();
            }
        } else {
            $eniture_plugins = get_option('EN_Plugins');
            $plugins_array = json_decode($eniture_plugins, true);
            $plugins_array = !empty($plugins_array) && is_array($plugins_array) ? $plugins_array : array();
            $key = array_search('daylight', $plugins_array);
            if ($key !== false) {
                unset($plugins_array[$key]);
            }
            update_option('EN_Plugins', json_encode($plugins_array));
        }
    }
}