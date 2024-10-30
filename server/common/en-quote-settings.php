<?php

/**
 * App Name settings.
 */

namespace EnDaylightQuoteSettingsDetail;

/**
 * Get and save settings.
 * Class EnDaylightQuoteSettingsDetail
 * @package EnDaylightQuoteSettingsDetail
 */
if (!class_exists('EnDaylightQuoteSettingsDetail')) {

    class EnDaylightQuoteSettingsDetail
    {

        static public $en_daylight_accessorial = [];

        /**
         * Set quote settings detail
         */
        static public function en_daylight_get_quote_settings()
        {
            $accessorials = [];
            $en_settings = json_decode(EN_DAYLIGHT_SET_QUOTE_SETTINGS, true);
            $en_settings['liftgate_delivery_option'] == 'yes' ? $accessorials['accessorials']['LIFT'] = 'Lift Gate Delivery' : "";
            $en_settings['liftgate_delivery'] == 'yes' ? $accessorials['accessorials']['LIFT'] = 'Lift Gate Delivery' : "";
            $en_settings['residential_delivery'] == 'yes' ? $accessorials['accessorials']['RESD'] = 'Residential Delivery' : "";
            $accessorials['handlingUnitWeight'] = $en_settings['handling_unit_weight'];
            $accessorials['maxWeightPerHandlingUnit'] = $en_settings['maximum_handling_unit_weight'];

            return $accessorials;
        }

        /**
         * Set quote settings detail
         */
        static public function en_daylight_always_accessorials()
        {
            $accessorials = [];
            $en_settings = self::en_daylight_quote_settings();
            $en_settings['liftgate_delivery'] == 'yes' ? $accessorials[] = 'L' : "";
            $en_settings['residential_delivery'] == 'yes' ? $accessorials[] = 'R' : "";

            return $accessorials;
        }

        /**
         * Set quote settings detail
         */
        static public function en_daylight_quote_settings()
        {
            $enable_carriers = [];
            $rating_method = 'Cheapest';
            $quote_settings_label = get_option('en_quote_settings_custom_label_daylight');

            $quote_settings = [
                'transit_days' => get_option('en_quote_settings_show_delivery_estimate_daylight'),
                'own_freight' => get_option('en_quote_settings_own_arrangment_daylight'),
                'own_freight_label' => get_option('en_quote_settings_text_for_own_arrangment_daylight'),
                'total_carriers' => get_option('en_quote_settings_number_of_options_daylight'),
                'rating_method' => (strlen($rating_method)) > 0 ? $rating_method : "Cheapest",
                'en_settings_label' => ($rating_method == "average_rate" || $rating_method == "Cheapest") ? $quote_settings_label : "",
                'handling_unit_weight' => get_option('en_quote_settings_handling_unit_weight_daylight'),
                'maximum_handling_unit_weight' => get_option('maximum_handling_weight_daylight'),
                'handling_fee' => get_option('en_quote_settings_handling_fee_daylight'),
                'enable_carriers' => $enable_carriers,
                'liftgate_delivery' => get_option('en_quote_settings_liftgate_delivery_daylight'),
                'liftgate_delivery_option' => get_option('daylight_liftgate_delivery_as_option'),
                'residential_delivery' => get_option('en_quote_settings_residential_delivery_daylight'),
                'liftgate_resid_delivery' => get_option('en_woo_addons_liftgate_with_auto_residential'),
                'custom_error_message' => get_option('en_quote_settings_checkout_error_message_daylight'),
                'custom_error_enabled' => get_option('en_quote_settings_option_select_when_unable_retrieve_shipping_daylight'),
                'handling_weight' => get_option('en_quote_settings_handling_unit_weight_daylight'),
                'maximum_handling_weight' => get_option('maximum_handling_weight_daylight'),
            ];

            return $quote_settings;
        }

        /**
         * Get quote settings detail
         * @param array $en_settings
         * @return array
         */
        static public function en_daylight_compare_accessorial($en_settings)
        {
            self::$en_daylight_accessorial[] = ['S'];
            $en_settings['liftgate_delivery_option'] == 'yes' ? self::$en_daylight_accessorial[] = ['L'] : "";

            return self::$en_daylight_accessorial;
        }

    }

}