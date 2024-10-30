<?php

/**
 * Shipping quotes event handler.
 * Class EnDaylightCarrierShippingRates
 */
if (!class_exists('EnDaylightCarrierShippingRates')) {

    class EnDaylightCarrierShippingRates
    {

        /**
         * Hook for call.
         * EnDaylightCarrierShippingRates constructor.
         */
        public function __construct()
        {
            /**
             * Load class for shipping rates
             */
            add_action('woocommerce_shipping_init', 'en_daylight_shipping_rates');
        }

    }

}

/**
 * Hook function for call.
 */
if (!function_exists('en_daylight_shipping_rates')) {

    function en_daylight_shipping_rates()
    {

        /**
         * Add class for shipping rates
         */
        class EnDaylightShippingRates extends WC_Shipping_Method
        {

            public $en_package = [];
            public $small_package = [];
            public $ltl_package = [];
            // FDO
            public $en_fdo_meta_data = [];
            public $en_fdo_meta_data_third_party = [];

            /**
             * Hook for call
             * EnDaylightShippingRates constructor.
             * @param int $instance_id
             */
            public function __construct($instance_id = 0)
            {
                $this->id = 'daylight';
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Daylight');
                $this->method_description = __('Shipping rates from Daylight.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = 'LTL Freight Quotes - Daylight';
                $this->init();
            }

            /**
             * Let's start init function
             */
            public function init()
            {
                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
            }

            /**
             * Enable woocommerce shipping for Daylight
             */
            public function init_form_fields()
            {
                $this->instance_form_fields = [
                    'enabled' => [
                        'title' => __('Enable / Disable', 'daylight'),
                        'type' => 'checkbox',
                        'label' => __('Enable This Shipping Service', 'daylight'),
                        'default' => 'no',
                        'id' => 'en_daylight_enable_disable_shipping'
                    ]
                ];
            }

            /**
             * Calculate shipping rates woocommerce
             * @param array $package
             * @return array|void
             */
            public function calculate_shipping($package = [])
            {
                // Eniture Debug Mood
                do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Plan ", EN_DAYLIGHT_PLAN);
                do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Plan Message ", EN_DAYLIGHT_PLAN_MESSAGE);

                // Eniture Execution Time
                $en_calculate_shipping_start = microtime(true);

                $en_package = apply_filters('en_package_converter', []);
                if (empty($en_package)) {
                    $this->en_package = $en_package = \EnDaylightPackage\EnDaylightPackage::en_package_converter($package);
                    add_filter('en_package_converter', [$this, 'en_recently_package_converter'], 10, 1);

                    // Eniture Debug Mood
                    do_action("eniture_debug_mood", "Eniture Packages", $en_package);
                }

                $en_package = $this->en_filter_eniture_shipments($en_package);

                $reasons = apply_filters('en_daylight_reason_quotes_not_returned', []);

                // -100% Handling Fee
                $handling_fee = get_option('en_quote_settings_handling_fee_daylight');
                if (!empty($handling_fee) && $handling_fee == '-100%') {
                    $rates = array(
                        'id' => $this->id . ':' . 'free',
                        'label' => 'Free Shipping',
                        'cost' => 0,
                        'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                        'plugin_type' => 'ltl',
                        'owned_by' => 'eniture'
                    );
                    $this->add_rate($rates);

                    return [];
                }

                if (!empty($this->ltl_package) && empty($reasons)) {

                    // Eniture Debug Mood
                    do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Package ", $en_package);

                    add_filter('en_eniture_shipment', [$this, 'en_eniture_shipment']);

                    $en_package = array_merge(json_decode(EN_DAYLIGHT_GET_CONNECTION_SETTINGS, true), $en_package, json_decode(EN_DAYLIGHT_GET_QUOTE_SETTINGS, true));

                    $response = \EnDaylightCurl\EnDaylightCurl::en_daylight_sent_http_request(EN_DAYLIGHT_HITTING_API_URL, $en_package, 'POST', 'Quotes');

                    // Get Small package quotes charges.
                    $en_small_package_quotes = EnDaylightSPQ\EnDaylightSPQ::en_small_package_quotes($package, $this->small_package);

                    $en_small_package_charges = 0;
                    if (!empty($en_small_package_quotes)) {
                        $en_small_package_quote = reset($en_small_package_quotes);
                        $en_small_package_charges = (isset($en_small_package_quote['cost'])) ? $en_small_package_quote['cost'] : 0;
                    }

                    $en_rates = \EnDaylightResponse\EnDaylightResponse::en_rates(json_decode($response, true), $en_package, $en_small_package_quotes);

                    // FDO
                    if (isset($en_small_package_quote['meta_data']['en_fdo_meta_data'])) {
                        if (!empty($en_small_package_quote['meta_data']['en_fdo_meta_data']) && !is_array($en_small_package_quote['meta_data']['en_fdo_meta_data'])) {
                            $en_third_party_fdo_meta_data = json_decode($en_small_package_quote['meta_data']['en_fdo_meta_data'], true);
                            isset($en_third_party_fdo_meta_data['data']) ? $en_small_package_quote['meta_data']['en_fdo_meta_data'] = $en_third_party_fdo_meta_data['data'] : '';
                        }

                        $this->en_fdo_meta_data_third_party = (isset($en_small_package_quote['meta_data']['en_fdo_meta_data']['address'])) ? [$en_small_package_quote['meta_data']['en_fdo_meta_data']] : $en_small_package_quote['meta_data']['en_fdo_meta_data'];
                    }

                    $accessorials = [
                        'R' => 'residential delivery',
                        'L' => 'liftgate delivery',
                        'T' => 'tailgate delivery',
                    ];

                    // Eniture Debug Mood
                    do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Rates ", $en_rates);

                    // Images for FDO
                    $image_urls = apply_filters('en_fdo_image_urls_merge', []);

                    foreach ($en_rates as $accessorial => $rate) {
                        if (isset($rate['label_sufex']) && !empty($rate['label_sufex'])) {
                            $label_sufex = array_intersect_key($accessorials, array_flip($rate['label_sufex']));
                            $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
                            if (stripos(implode($all_plugins), 'residential-address-detection.php') || is_plugin_active_for_network('residential-address-detection/residential-address-detection.php')) {
                                if(get_option('suspend_automatic_detection_of_residential_addresses') != 'yes') {
                                    $rad_status = get_option('residential_delivery_options_disclosure_types_to') != 'not_show_r_checkout';
                                    if($rad_status != true && isset($label_sufex['R'])) {
                                        unset($label_sufex['R']);
                                    }
                                }
                            }
                            $rate['label'] .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';
                            isset($rate['cost']) && $en_small_package_charges > 0 ?
                                $rate['cost'] = $rate['cost'] + $en_small_package_charges : 0;

                            // Order widget detail set
                            if (isset($rate['min_prices'], $rate['en_fdo_meta_data'])) {
                                // FDO
                                $en_fdo_meta_data = $rate['en_fdo_meta_data'];
                                (!empty($this->en_fdo_meta_data_third_party)) ? $en_fdo_meta_data = array_merge($en_fdo_meta_data, $this->en_fdo_meta_data_third_party) : '';
                                $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode(['data' => $en_fdo_meta_data, 'shipment' => 'multiple']);
                                $rate['min_prices'] = !empty($en_small_package_quotes) ? array_merge($rate['min_prices'], $en_small_package_quotes) : $rate['min_prices'];
                                $rate['meta_data']['min_prices'] = wp_json_encode($rate['min_prices']);
                                unset($rate['min_prices']);
                            } else {
                                // FDO
                                $en_fdo_meta_data = (isset($rate['meta_data']['en_fdo_meta_data'])) ? [$rate['meta_data']['en_fdo_meta_data']] : [];
                                $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode(['data' => $en_fdo_meta_data, 'shipment' => 'single']);
                            }

                            // Images for FDO
                            $rate['meta_data']['en_fdo_image_urls'] = wp_json_encode($image_urls);
                            $rate['id'] = isset($rate['id']) && is_string($rate['id']) ? $this->id . ':' . $rate['id'] : '';
                        }

                        $this->add_rate($rate);
                    }
                }

                // Eniture Execution Time
                $en_calculate_shipping_end = microtime(true) - $en_calculate_shipping_start;
                do_action("eniture_debug_mood", EN_DAYLIGHT_NAME . " Total Execution Time ", $en_calculate_shipping_end);
            }

            /**
             * List down both ltl or small packages
             * @param array $en_package
             * @return mixed
             */
            public function en_filter_eniture_shipments($en_package)
            {
                if (isset($en_package['shipment_type']) && is_array($en_package['shipment_type'])) {
                    foreach ($en_package['shipment_type'] as $origin_zip => $shipment) {
                        if (isset($shipment['SMALL']) && count($shipment) == 1) {
                            $this->small_package[$origin_zip] = EN_DAYLIGHT_DECLARED_TRUE;
                            unset($en_package['commdityDetails'][$origin_zip]);
                        }
                    }
                }

                return $this->ltl_package = $en_package;
            }

            /**
             * Get last used array of packages
             * @param array $package
             * @return array
             */
            public function en_recently_package_converter($package)
            {
                return array_merge($package, $this->en_package);
            }

            /**
             * Set flag eniture shipment exist or not
             * @param array $eniture_shipment
             * @return array
             */
            public function en_eniture_shipment($eniture_shipment)
            {
                return array_merge($eniture_shipment, ['LTL' => $this->ltl_package]);
            }

        }

    }

}