<?php

/**
 * Test connection details.
 */

namespace EnDaylightConnectionSettings;

/**
 * Add array for test connection.
 * Class EnDaylightConnectionSettings
 * @package EnDaylightConnectionSettings
 */
if (!class_exists('EnDaylightConnectionSettings')) {

    class EnDaylightConnectionSettings
    {

        static $get_connection_details = [];

        /**
         * Connection settings template.
         * @return array
         */
        static public function en_load()
        {
            echo '<div class="en_daylight_connection_settings">';

            $start_settings = [
                'en_connection_settings_start_daylight' => [
                    'name' => __('', 'woocommerce-settings-daylight'),
                    'type' => 'title',
                    'id' => 'en_connection_settings_daylight',
                ],
            ];

            // App Name Connection Settings Detail
            $eniture_settings = self::en_set_connection_settings_detail();

            $end_settings = [
                'en_connection_settings_end_daylight' => [
                    'type' => 'sectionend',
                    'id' => 'en_connection_settings_end_daylight'
                ]
            ];

            $settings = array_merge($start_settings, $eniture_settings, $end_settings);

            return $settings;
        }

        /**
         * Connection Settings Detail
         * @return array
         */
        static public function en_get_connection_settings_detail()
        {
            $connection_request = self::en_static_request_detail();
            $en_request_indexing = json_decode(EN_DAYLIGHT_SET_CONNECTION_SETTINGS, true);
            foreach ($en_request_indexing as $key => $value) {
                $saved_connection_detail = get_option($key);
                $connection_request[$value['eniture_action']] = $saved_connection_detail;
                strlen($saved_connection_detail) > 0 ?
                    self::$get_connection_details[$value['eniture_action']] = $saved_connection_detail : '';
            }

            add_filter('en_daylight_reason_quotes_not_returned', [__CLASS__, 'en_daylight_reason_quotes_not_returned'], 99, 1);

            return $connection_request;
        }

        /**
         * Saving reasons to show proper error message on the cart or checkout page
         * When quotes are not returning
         * @param array $reasons
         * @return array
         */
        static public function en_daylight_reason_quotes_not_returned($reasons)
        {
            return empty(self::$get_connection_details) ? array_merge($reasons, [EN_DAYLIGHT_711]) : $reasons;
        }

        /**
         * Static Detail Set
         * @return array
         */
        static public function en_static_request_detail()
        {
            return
                [
                    'serverName' => EN_DAYLIGHT_SERVER_NAME,
                    'platform' => 'WordPress',
                    'carrierType' => 'LTL',
                    'carrierName' => 'daylight',
                    'carrierMode' => 'pro',
                    'requestVersion' => '2.0',
                    'requestKey' => time(),
                ];
        }

        /**
         * Connection Settings Detail Set
         * @return array
         */
        static public function en_set_connection_settings_detail()
        {
            return
                [
                    'en_connection_settings_username_daylight' => [
                        'eniture_action' => 'username',
                        'name' => __('Username ', 'woocommerce-settings-daylight'),
                        'type' => 'text',
                        'desc' => __('', 'woocommerce-settings-daylight'),
                        'id' => 'en_connection_settings_username_daylight'
                    ],

                    'en_connection_settings_password_daylight' => [
                        'eniture_action' => 'password',
                        'name' => __('Password ', 'woocommerce-settings-daylight'),
                        'type' => 'text',
                        'desc' => __('', 'woocommerce-settings-daylight'),
                        'id' => 'en_connection_settings_password_daylight'
                    ],

                    'en_connection_settings_account_number_daylight' => [
                        'eniture_action' => 'accountNumber',
                        'name' => __('Account Number ', 'woocommerce-settings-daylight'),
                        'type' => 'text',
                        'desc' => __('', 'woocommerce-settings-daylight'),
                        'id' => 'en_connection_settings_account_number_daylight'
                    ],

                    'en_connection_settings_license_key_daylight' => [
                        'eniture_action' => 'licenseKey',
                        'name' => __('Eniture API Key ', 'woocommerce-settings-daylight'),
                        'type' => 'text',
                        'desc' => __('Obtain a Eniture API Key from <a href="' . EN_DAYLIGHT_ROOT_URL_PRODUCTS . '" target="_blank" >eniture.com </a>', 'woocommerce-settings-daylight'),
                        'id' => 'en_connection_settings_license_key_daylight'
                    ],
                ];
        }

    }

}