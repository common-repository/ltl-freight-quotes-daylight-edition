<?php

/**
 * Third party rates used in eniture shipping services.
 */

namespace EnDaylightOtherRates;

/**
 * Filter other rates will be shown on the cart|checkout page.
 * Class EnDaylightOtherRates
 * @package EnDaylightOtherRates
 */
if (!class_exists('EnDaylightOtherRates')) {

    class EnDaylightOtherRates
    {

        /**
         * @param array $instor_pickup_local_delivery
         * @param string $en_is_shipment
         * @param array $en_origin_address
         * @param array $api_rates
         * @param array $en_settings
         * @return array
         */
        static public function en_extra_custom_services($instor_pickup_local_delivery, $en_is_shipment, $en_origin_address, $api_rates, $en_settings)
        {
            $rates = [];
            if (!empty($instor_pickup_local_delivery) && $en_is_shipment === 'en_single_shipment') {
                $phone_instore = $address = $city = $state = $zip = $senderDescInStorePickup = $senderDescLocalDelivery = $suppressOtherRates = '';
                $feeLocalDelivery = 0;
                extract($en_origin_address);
                $label = strlen($senderDescInStorePickup) > 0 ? $senderDescInStorePickup : 'In-store pick up';
                // Origin terminal address
                $total_distance = isset($instor_pickup_local_delivery['totalDistance']) ? $instor_pickup_local_delivery['totalDistance'] : '';
                strlen($total_distance) > 0 ? $label .= ': Free | ' . str_replace("mi", "miles", $total_distance) . ' away' : '';
                strlen($address) > 0 ? $label .= ' | ' . $address : '';
                strlen($city) > 0 ? $label .= ', ' . $city : '';
                strlen($state) > 0 ? $label .= ' ' . $state : '';
                strlen($zip) > 0 ? $label .= ' ' . $zip : '';
                strlen($phone_instore) > 0 ? $label .= ' | ' . $phone_instore : '';

                if (isset($instor_pickup_local_delivery['inStorePickup']['status']) &&
                    $instor_pickup_local_delivery['inStorePickup']['status'] === '1') {
                    $rates[] = array(
                        'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                        'plugin_type' => 'ltl',
                        'owned_by' => 'eniture',
                        'id' => 'daylight:' . 'in-store-pick-up',
                        'cost' => 0,
                        'label' => $label,
                    );
                }

                if (isset($instor_pickup_local_delivery['localDelivery']['status']) &&
                    $instor_pickup_local_delivery['localDelivery']['status'] === '1') {
                    $rates[] = array(
                        'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                        'plugin_type' => 'ltl',
                        'owned_by' => 'eniture',
                        'id' => 'daylight:' . 'local-delivery',
                        'cost' => $feeLocalDelivery > 0 ? $feeLocalDelivery : 0,
                        'label' => strlen($senderDescLocalDelivery) > 0 ? $senderDescLocalDelivery : 'Local delivery',
                    );
                }

                if ($suppressOtherRates == 'on' && !empty($rates)) {
                    $api_rates = [];
                }
            }

            if (isset($en_settings['own_freight']) && $en_settings['own_freight'] === 'yes') {
                $own_freight_label = (strlen($en_settings['own_freight_label']) > 0) ?
                    $en_settings['own_freight_label'] : "I'll arrange my own freight";

                $rates[] = [
                    'plugin_name' => EN_DAYLIGHT_SHIPPING_NAME,
                    'plugin_type' => 'ltl',
                    'owned_by' => 'eniture',
                    'id' => 'daylight:' . 'en_own_freight',
                    'cost' => 0,
                    'label' => $own_freight_label,
                ];
            }

            $api_rates = array_merge($rates, $api_rates);

            return $api_rates;
        }

    }

}