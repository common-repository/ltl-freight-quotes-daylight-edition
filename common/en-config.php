<?php

/**
 * App Name details.
 */

namespace EnDaylightConfig;

use EnDaylightConnectionSettings\EnDaylightConnectionSettings;
use EnDaylightQuoteSettingsDetail\EnDaylightQuoteSettingsDetail;

/**
 * Config values.
 * Class EnDaylightConfig
 * @package EnDaylightConfig
 */
if (!class_exists('EnDaylightConfig')) {

    class EnDaylightConfig
    {

        /**
         * Save config settings
         */
        static public function do_config()
        {
            define('EN_DAYLIGHT_PLAN', get_option('en_daylight_plan_number'));
            !empty(get_option('en_daylight_plan_message')) ? define('EN_DAYLIGHT_PLAN_MESSAGE', get_option('en_daylight_plan_message')) : define('EN_DAYLIGHT_PLAN_MESSAGE', EN_DAYLIGHT_704);
            define('EN_DAYLIGHT_NAME', 'Daylight');
            define('EN_DAYLIGHT_PLUGIN_URL', plugins_url());
            define('EN_DAYLIGHT_ABSPATH', ABSPATH);
            define('EN_DAYLIGHT_DIR', plugins_url(EN_DAYLIGHT_MAIN_DIR));
            define('EN_DAYLIGHT_DIR_FILE', plugin_dir_url(EN_DAYLIGHT_MAIN_FILE));
            define('EN_DAYLIGHT_FILE', plugins_url(EN_DAYLIGHT_MAIN_FILE));
            define('EN_DAYLIGHT_BASE_NAME', plugin_basename(EN_DAYLIGHT_MAIN_FILE));
            define('EN_DAYLIGHT_SERVER_NAME', self::en_get_server_name());

            define('EN_DAYLIGHT_DECLARED_ZERO', 0);
            define('EN_DAYLIGHT_DECLARED_ONE', 1);
            define('EN_DAYLIGHT_DECLARED_ARRAY', []);
            define('EN_DAYLIGHT_DECLARED_FALSE', false);
            define('EN_DAYLIGHT_DECLARED_TRUE', true);
            define('EN_DAYLIGHT_SHIPPING_NAME', 'daylight');

            $weight_threshold = get_option('en_weight_threshold_lfq');
            $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;
            define('EN_DAYLIGHT_SHIPMENT_WEIGHT_EXCEEDS_PRICE', $weight_threshold);
            define('EN_DAYLIGHT_SHIPMENT_WEIGHT_EXCEEDS', get_option('en_plugins_return_LTL_quotes'));
            if (!defined('EN_DAYLIGHT_ROOT_URL'))
            {
                define('EN_DAYLIGHT_ROOT_URL', 'https://eniture.com');
            }
            define('EN_DAYLIGHT_ROOT_URL_PRODUCTS', EN_DAYLIGHT_ROOT_URL . '/products/');
            define('EN_DAYLIGHT_RAD_URL', EN_DAYLIGHT_ROOT_URL . '/woocommerce-residential-address-detection/');
            
            define('EN_DAYLIGHT_SUPPORT_URL', 'https://support.eniture.com/');
            
            
            define('EN_DAYLIGHT_DOCUMENTATION_URL', EN_DAYLIGHT_ROOT_URL . '/woocommerce-daylight-ltl-freight/');
            
            define('EN_DAYLIGHT_ROOT_URL_QUOTES', 'https://ws084.eniture.com');
            define('EN_DAYLIGHT_HITTING_API_URL', EN_DAYLIGHT_ROOT_URL_QUOTES . '/daylight/quotes.php');
            define('EN_DAYLIGHT_ADDRESS_HITTING_URL', EN_DAYLIGHT_ROOT_URL_QUOTES . '/addon/google-location.php');
            define('EN_DAYLIGHT_PLAN_HITTING_URL', EN_DAYLIGHT_ROOT_URL_QUOTES . '/web-hooks/subscription-plans/create-plugin-webhook.php?');
            define('EN_DAYLIGHT_ORDER_EXPORT_HITTING_URL', 'https://analytic-data.eniture.com/index.php');

            define('EN_DAYLIGHT_SET_CONNECTION_SETTINGS', wp_json_encode(EnDaylightConnectionSettings::en_set_connection_settings_detail()));
            define('EN_DAYLIGHT_GET_CONNECTION_SETTINGS', wp_json_encode(EnDaylightConnectionSettings::en_get_connection_settings_detail()));
            define('EN_DAYLIGHT_SET_QUOTE_SETTINGS', wp_json_encode(EnDaylightQuoteSettingsDetail::en_daylight_quote_settings()));
            define('EN_DAYLIGHT_GET_QUOTE_SETTINGS', wp_json_encode(EnDaylightQuoteSettingsDetail::en_daylight_get_quote_settings()));

            $en_app_set_quote_settings = json_decode(EN_DAYLIGHT_SET_QUOTE_SETTINGS, true);

            define('EN_DAYLIGHT_ALWAYS_ACCESSORIAL', wp_json_encode(EnDaylightQuoteSettingsDetail::en_daylight_always_accessorials($en_app_set_quote_settings)));
            define('EN_DAYLIGHT_ACCESSORIAL', wp_json_encode(EnDaylightQuoteSettingsDetail::en_daylight_compare_accessorial($en_app_set_quote_settings)));
        }

        /**
         * Get Host
         * @param type $url
         * @return type
         */
        static public function en_get_host($url)
        {
            $parse_url = parse_url(trim($url));
            if (isset($parse_url['host'])) {
                $host = $parse_url['host'];
            } else {
                $path = explode('/', $parse_url['path']);
                $host = $path[0];
            }
            return trim($host);
        }

        /**
         * Get Domain Name
         */
        static public function en_get_server_name()
        {
            global $wp;
            $wp_request = (isset($wp->request)) ? $wp->request : '';
            $url = home_url($wp_request);
            return self::en_get_host($url);
        }

    }

}
