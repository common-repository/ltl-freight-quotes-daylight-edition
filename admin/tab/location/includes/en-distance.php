<?php

namespace EnDaylightDistance;

use EnDaylightCurl\EnDaylightCurl;

if (!class_exists('EnDaylightDistance')) {

    class EnDaylightDistance
    {

        static public function get_address($map_address, $en_access_level, $en_destination_address = [])
        {
            $post_data = array(
                'acessLevel' => $en_access_level,
                'address' => $map_address,
                'originAddresses' => $map_address,
                'destinationAddress' => (isset($en_destination_address)) ? $en_destination_address : '',
                'eniureLicenceKey' => get_option('en_connection_settings_license_key_daylight'),
                'ServerName' => EN_DAYLIGHT_SERVER_NAME,
            );

            return EnDaylightCurl::en_daylight_sent_http_request(EN_DAYLIGHT_ADDRESS_HITTING_URL, $post_data, 'POST', 'Address');
        }

    }

}