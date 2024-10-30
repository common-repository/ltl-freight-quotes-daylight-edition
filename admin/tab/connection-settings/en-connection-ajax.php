<?php

/**
 * Curl http request.
 */

namespace EnDaylightTestConnection;

use EnDaylightCurl\EnDaylightCurl;

/**
 * Test connection request.
 * Class EnDaylightTestConnection
 * @package EnDaylightTestConnection
 */
if (!class_exists('EnDaylightTestConnection')) {

    class EnDaylightTestConnection
    {

        /**
         * Hook in ajax handlers.
         */
        public function __construct()
        {
            add_action('wp_ajax_nopriv_en_daylight_test_connection', [$this, 'en_daylight_test_connection']);
            add_action('wp_ajax_en_daylight_test_connection', [$this, 'en_daylight_test_connection']);
        }

        /**
         * Handle Connection Settings Ajax Request
         */
        public function en_daylight_test_connection()
        {
            $en_post_data = (isset($_POST['en_post_data'])) ? $_POST['en_post_data'] : '';
            $en_request_indexing = json_decode(EN_DAYLIGHT_SET_CONNECTION_SETTINGS, true);
            $en_connection_request = json_decode(EN_DAYLIGHT_GET_CONNECTION_SETTINGS, true);

            foreach ($en_post_data as $key => $value) {
                $en_request_name = (isset($value['name'])) ? sanitize_text_field($value['name']) : '';
                $en_request_value = (isset($value['value'])) ? sanitize_text_field($value['value']) : '';

                $en_connection_request[$en_request_indexing[$en_request_name]['eniture_action']] = $en_request_value;
            }

            $en_connection_request['carrierMode'] = 'test';
            $en_connection_request = apply_filters('en_daylight_add_connection_request', $en_connection_request);

            echo EnDaylightCurl::en_daylight_sent_http_request(
                EN_DAYLIGHT_HITTING_API_URL, $en_connection_request, 'POST', 'Connection'
            );
            exit;
        }

    }

}