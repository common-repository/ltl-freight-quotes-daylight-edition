<?php

/**
 * Customize the api response.
 */

namespace EnDaylightResponse;

use EnDaylightFdo\EnDaylightFdo;
use EnDaylightFilterQuotes\EnDaylightFilterQuotes;
use EnDaylightOtherRates\EnDaylightOtherRates;
use EnDaylightVersionCompact\EnDaylightVersionCompact;

/**
 * Compile the rates.
 * Class EnDaylightResponse
 * @package EnDaylightResponse
 */
if (!class_exists('EnDaylightResponse')) {

    class EnDaylightResponse
    {
        static public $en_step_for_rates = [];
        static public $en_small_package_quotes = [];
        static public $en_step_for_sender_origin = [];
        static public $en_step_for_product_name = [];
        static public $en_quotes_info_api = [];
        static public $en_accessorial = [];
        static public $en_always_accessorial = [];
        static public $en_settings = [];
        static public $en_package = [];
        static public $en_origin_address = [];
        static public $en_is_shipment = '';
        static public $en_auto_residential_status = '';
        static public $en_hazardous_status = '';
        static public $rates;
        static public $standard_packaging = [];
        // FDO
        static public $fdo;
        static public $en_fdo_meta_data = [];

        /**
         * Address set for order widget
         * @param array $sender_origin
         * @return string
         */
        static public function en_step_for_sender_origin($sender_origin)
        {
            return $sender_origin['senderLocation'] . ": " . $sender_origin['senderCity'] . ", " . $sender_origin['senderState'] . " " . $sender_origin['senderZip'];
        }

        /**
         * filter detail for order widget detail
         * @param array $en_package
         * @param mixed $key
         */
        static public function en_save_detail_for_order_widget($en_package, $key)
        {
            // FDO
            self::$fdo = new EnDaylightFdo();
            self::$en_fdo_meta_data = self::$fdo::en_cart_package($en_package, $key);
            self::$en_step_for_sender_origin = self::en_step_for_sender_origin($en_package['originAddress'][$key]);
            self::$en_step_for_product_name = (isset($en_package['product_name'][$key])) ? $en_package['product_name'][$key] : [];
        }

        /**
         * Shipping rates
         * @param array $response
         * @param array $en_package
         * @return array
         */
        static public function en_rates($response, $en_package, $en_small_package_quotes)
        {
            self::$fdo = new EnDaylightFdo();
            self::$rates = $instor_pickup_local_delivery = [];
            self::$en_package = $en_package;
            self::$en_small_package_quotes = $en_small_package_quotes;
            $en_response = (!empty($response) && is_array($response)) ? $response : [];
            $en_response = self::en_is_shipment_api_response($en_response);
            $autoResidentialSubscriptionExpired = $hazardousStatus = $autoResidentialStatus = '';
            extract(self::$en_quotes_info_api);

            foreach ($en_response as $key => $value) {
                self::en_save_detail_for_order_widget(self::$en_package, $key);
                self::$en_step_for_rates = $value;

                // check for error
                $severity = self::en_sanitize_rate('severity', '');
                if (is_string($severity) && strlen($severity) > 0 && strtolower($severity) == 'error') {
                    return [];
                }

                $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");
                $auto_renew_plan = get_option("auto_residential_delivery_plan_auto_renew");

                if (($auto_renew_plan == "disable") &&
                    ($residential_detecion_flag == "yes") && $autoResidentialSubscriptionExpired == 1) {
                    update_option("en_woo_addons_auto_residential_detecion_flag", "no");
                }

                (isset(self::$en_package['originAddress'][$key])) ? self::$en_origin_address = self::$en_package['originAddress'][$key] : '';

                self::$en_auto_residential_status = $autoResidentialStatus;
                self::$en_hazardous_status = $hazardousStatus;

                $instor_pickup_local_delivery = self::en_sanitize_rate('InstorPickupLocalDelivery', []);
                // Standard Packaging
                self::$standard_packaging = self::en_sanitize_rate('standardPackagingData', []);

                $origin_level_markup = isset($en_package['originAddress'][$key]['origin_markup']) ? $en_package['originAddress'][$key]['origin_markup'] : 0;
                $product_level_markup = 0;
                $products = $en_package['commdityDetails'][$key];
                
                if (!empty($products)) {
                    foreach ($products as $pdct) {
                        $product_level_markup += !empty($pdct['markup']) ? $pdct['markup'] : 0;
                    }
                }

                self::en_arrange_rates(self::en_sanitize_rate('q', []), $origin_level_markup, $product_level_markup);
            }

            self::$rates = EnDaylightOtherRates::en_extra_custom_services
            (
                $instor_pickup_local_delivery, self::$en_is_shipment, self::$en_origin_address, self::$rates, self::$en_settings
            );

            return self::$rates;
        }

        /**
         * Multi shipment query
         * @param array $en_rates
         * @param string $accessorial
         */
        static public function en_multi_shipment($en_rates, $accessorial)
        {
            $en_rates = (isset($en_rates) && (is_array($en_rates))) ? array_slice($en_rates, 0, 1) : [];
            $en_calculated_cost = array_sum(EnDaylightVersionCompact::en_array_column($en_rates, 'cost'));

            // FDO
            $en_fdo_meta_data = [];
            if (!isset($en_rates['meta_data']) && !empty($en_rates) && is_array($en_rates)) {
                $rate = reset($en_rates);
                $en_fdo_meta_data[] = (isset($rate['meta_data']['en_fdo_meta_data'])) ? $rate['meta_data']['en_fdo_meta_data'] : [];
            }

            if (isset(self::$rates[$accessorial])) {
                self::$rates[$accessorial]['id'] = isset(self::$rates[$accessorial]['id']) ? self::$rates[$accessorial]['id'] : EnDaylightFilterQuotes::rand_string();
                self::$rates[$accessorial]['cost'] += $en_calculated_cost;
                self::$rates[$accessorial]['min_prices'] = array_merge(self::$rates[$accessorial]['min_prices'], $en_rates);
                // FDO
                self::$rates[$accessorial]['en_fdo_meta_data'] = array_merge(self::$rates[$accessorial]['en_fdo_meta_data'], $en_fdo_meta_data);
            } else {
                self::$rates[$accessorial] = [
                    'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                    'plugin_type' => 'ltl',
                    'owned_by' => 'eniture',
                    'id' => 'daylight_' . $accessorial,
                    'label' => 'Freight',
                    'cost' => $en_calculated_cost,
                    'label_sufex' => str_split($accessorial),
                    'min_prices' => $en_rates,
                    // FDO
                    'en_fdo_meta_data' => $en_fdo_meta_data
                ];
            }
        }

        /**
         * Single shipment query
         * @param array $en_rates
         * @param string $accessorial
         */
        static public function en_single_shipment($en_rates, $accessorial)
        {
            self::$rates = array_merge(self::$rates, $en_rates);
        }

        /**
         * Sanitize the value from array
         * @param string $index
         * @param dynamic $is_not_matched
         * @return dynamic mixed
         */
        static public function en_sanitize_rate($index, $is_not_matched)
        {
            return (isset(self::$en_step_for_rates[$index])) ? self::$en_step_for_rates[$index] : $is_not_matched;
        }

        /**
         * There is single or multiple shipment
         * @param array $en_response
         */
        static public function en_is_shipment_api_response($en_response)
        {
            if (isset($en_response['quotesInfo'])) {
                self::$en_quotes_info_api = $en_response['quotesInfo'];
                unset($en_response['quotesInfo']);
            }
            self::$en_is_shipment = count($en_response) > 1 || count(self::$en_small_package_quotes) > 0 ? 'en_multi_shipment' : 'en_single_shipment';
            return $en_response;
        }

        /**
         * Get accessorials prices from api response
         * @param array $accessorials
         * @return array
         */
        static public function en_get_accessorials_prices($accessorials)
        {
            $surcharges = [];
            $mapp_surcharges = [
                'Residential Delivery' => 'R',
                'Lift Gate Delivery' => 'L',
            ];

            foreach ($accessorials as $index => $accessorial) {
                $key = (isset($accessorial['reqAccessorial'])) ? $accessorial['reqAccessorial'] : '';
                $amount = (isset($accessorial['accCharge'])) ? $accessorial['accCharge'] : 0;
                if (isset($mapp_surcharges[$key])) {
                    in_array($mapp_surcharges[$key], self::$en_always_accessorial) ?
                        $amount = 0 : '';
                    self::$en_auto_residential_status == 'r' && $mapp_surcharges[$key] == 'R' ?
                        $amount = 0 : '';
                    $surcharges[$mapp_surcharges[$key]] = $amount;
                }
            }

            return $surcharges;
        }

        /**
         * Filter quotes
         * @param array $rates
         */
        static public function en_arrange_rates($rates, $origin_level_markup = '', $product_level_markup = '')
        {
            $en_rates = [];
            $en_sorting_rates = [];
            $en_count_rates = 0;

            $handling_fee = $en_settings_label = $rating_method = $transit_days = $enable_carriers = $liftgate_resid_delivery = $liftgate_delivery_option = '';
            self::$en_settings = json_decode(EN_DAYLIGHT_SET_QUOTE_SETTINGS, true);
            self::$en_accessorial = json_decode(EN_DAYLIGHT_ACCESSORIAL, true);
            self::$en_always_accessorial = json_decode(EN_DAYLIGHT_ALWAYS_ACCESSORIAL, true);
            extract(self::$en_settings);

            // Eniture Debug Mood
            do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Settings ", self::$en_settings);
            do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Accessorials ", self::$en_accessorial);

            // is quote settings label will be show or not
            switch (self::$en_is_shipment) {
                case 'en_single_shipment':
                    switch ($rating_method) {
                        case 'Cheapest' && strlen($en_settings_label) > 0:
                            $is_valid_label = EN_DAYLIGHT_DECLARED_TRUE;
                            break;
                    }
                    break;
                default:
                    $is_valid_label = EN_DAYLIGHT_DECLARED_FALSE;
                    break;
            }

            $en_rate = $rates;

            self::$en_step_for_rates = $en_rate;

            $en_total_net_charge = self::en_sanitize_rate('totalNetCharge', 0);
            if ($en_total_net_charge > 0) {
                // Product level markup
                if(!empty($product_level_markup)){
                    $en_total_net_charge = self::en_add_handling_fee($en_total_net_charge, $product_level_markup);
                }

                // origin level markup
                if(!empty($origin_level_markup)){
                    $en_total_net_charge = self::en_add_handling_fee($en_total_net_charge, $origin_level_markup);
                }
                
                $label = isset($is_valid_label) && $is_valid_label ? $en_settings_label : 'Freight';
                $calculated_transit_date = self::en_sanitize_rate('deliveryTimestamp', '');
                $label = ($rating_method != "average_rate" &&
                    is_string($calculated_transit_date) &&
                    self::$en_is_shipment == 'en_single_shipment' &&
                    $transit_days == "yes") ?
                    $label . ' (Expected delivery by ' . date('m-d-Y', strtotime($calculated_transit_date)) . ')' : $label;

                // make data for order widget detail
                $meta_data['quote_id'] = self::en_sanitize_rate('quoteId', '');
                $meta_data['service_type'] = $label;
                $meta_data['accessorials'] = EN_DAYLIGHT_ALWAYS_ACCESSORIAL;
                $meta_data['sender_origin'] = self::$en_step_for_sender_origin;
                $meta_data['product_name'] = wp_json_encode(self::$en_step_for_product_name);
                // Standard Packaging
                $meta_data['standard_packaging'] = wp_json_encode(self::$standard_packaging);

                // FDO
                $meta_data['en_fdo_meta_data'] = self::$en_fdo_meta_data;

                // standard rate
                $rate = [
                    'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                    'plugin_type' => 'ltl',
                    'owned_by' => 'eniture',
                    'id' => self::en_sanitize_rate('carrierCode', ''),
                    'label' => $label,
                    'cost' => $en_total_net_charge,
                    'surcharges' => self::en_get_accessorials_prices(self::en_sanitize_rate('completeAccessorialCharges', '')),
                    'meta_data' => $meta_data,
                    'transit_days' => $transit_days
                ];

                foreach (self::$en_accessorial as $key => $accessorial) {
                    $en_fliped_accessorial = array_flip($accessorial);

                    // When auto-rad detected
                    if (self::$en_auto_residential_status == 'r') {
                        $accessorial[] = 'R';

                        if ($liftgate_resid_delivery == 'yes') {
                            if ($liftgate_delivery_option == 'yes' && !in_array('L', $accessorial)) {
                                continue;
                            } else {
                                !isset($en_fliped_accessorial['L']) ? $en_fliped_accessorial['L'] = 1 : '';
                                !in_array('L', $accessorial) ? $accessorial[] = 'L' : '';
                            }
                        }
                    }

                    // When hazardous materials detected
                    self::$en_hazardous_status == 'h' ? $accessorial[] = 'H' : '';

                    self::$en_step_for_rates = $rate;

                    $en_accessorial_charges = array_diff_key(self::en_sanitize_rate('surcharges', []), $en_fliped_accessorial);

                    $en_accessorial_type = implode('', $accessorial);
                    self::$en_step_for_rates = $en_rates[$en_accessorial_type][$en_count_rates] = $rate;

                    // Cost of the rates
                    $en_sorting_rates
                    [$en_accessorial_type]
                    [$en_count_rates]['cost'] = // Used for sorting of rates
                    $en_rates
                    [$en_accessorial_type]
                    [$en_count_rates]['cost'] = self::en_sanitize_rate('cost', 0) - array_sum($en_accessorial_charges);

                    $en_rates
                    [$en_accessorial_type]
                    [$en_count_rates]['cost'] = self::en_add_handling_fee
                    (
                        $en_rates
                        [$en_accessorial_type]
                        [$en_count_rates]['cost'], $handling_fee
                    );

                    $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['label_sufex'] = wp_json_encode($accessorial);
                    $en_rates[$en_accessorial_type][$en_count_rates]['label_sufex'] = $accessorial;
                    $en_rates[$en_accessorial_type][$en_count_rates]['id'] .= 'daylight_' . implode('_', $accessorial);

                    // FDO
                    if (in_array('R', $accessorial)) {
                        $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true;
                    }
                    if (in_array('L', $accessorial)) {
                        $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                    }
                    if (in_array('N', $accessorial)) {
                        $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data']['accessorials']['notify'] = true;
                    }
                    $calculated_rate = $en_rates[$en_accessorial_type][$en_count_rates];
                    $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data']['rate'] = [
                        'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                        'plugin_type' => 'ltl',
                        'owned_by' => 'eniture',
                        'id' => $calculated_rate['id'],
                        'label' => $calculated_rate['label'],
                        'cost' => $calculated_rate['cost']
                    ];
                }

            }

            foreach ($en_rates as $accessorial => $services) {
                (!empty($en_rates[$accessorial])) ? array_multisort($en_sorting_rates[$accessorial], SORT_ASC, $en_rates[$accessorial]) : $en_rates[$accessorial] = [];
                $en_is_shipment = self::$en_is_shipment;
                self::$en_is_shipment(EnDaylightFilterQuotes::calculate_quotes($en_rates[$accessorial], self::$en_settings), $accessorial);
            }
        }

        /**
         * Generic function to add handling fee in cost of the rate
         * @param float $price
         * @param float $en_handling_fee
         * @return float
         */
        static public function en_add_handling_fee($price, $en_handling_fee)
        {
            $handling_fee = 0;
            if ($en_handling_fee != '' && $en_handling_fee != 0) {
                if (strrchr($en_handling_fee, "%")) {

                    $percent = (float)$en_handling_fee;
                    $handling_fee = (float)$price / 100 * $percent;
                } else {
                    $handling_fee = (float)$en_handling_fee;
                }
            }

            $handling_fee = self::en_smooth_round($handling_fee);
            $price = (float)$price + $handling_fee;
            return $price;
        }

        /**
         * Round the cost of the quote
         * @param float type $val
         * @param int type $min
         * @param int type $max
         * @return float type
         */
        static public function en_smooth_round($val, $min = 2)
        {
            return number_format($val, $min, ".", "");
        }

    }

}