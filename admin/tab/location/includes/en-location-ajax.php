<?php

namespace EnDaylightLocationAjax;

use EnDaylightDistance\EnDaylightDistance;
use EnDaylightDropshipTemplate\EnDaylightDropshipTemplate;
use EnDaylightWarehouse\EnDaylightWarehouse;
use EnDaylightWarehouseTemplate\EnDaylightWarehouseTemplate;

if (!class_exists('EnDaylightLocationAjax')) {

    class EnDaylightLocationAjax
    {

        public function __construct()
        {
            add_action('wp_ajax_nopriv_en_daylight_location_save_form_data', [$this, 'en_daylight_location_save_form_data']);
            add_action('wp_ajax_en_daylight_location_save_form_data', [$this, 'en_daylight_location_save_form_data']);

            add_action('wp_ajax_nopriv_en_daylight_get_location', [$this, 'en_daylight_get_location']);
            add_action('wp_ajax_en_daylight_get_location', [$this, 'en_daylight_get_location']);

            add_action('wp_ajax_nopriv_en_daylight_location_delete_row', [$this, 'en_daylight_location_delete_row']);
            add_action('wp_ajax_en_daylight_location_delete_row', [$this, 'en_daylight_location_delete_row']);
        }

        /**
         * Get response from api when we sent zip code
         */
        public function en_daylight_get_location()
        {
            if (isset($_POST['en_daylight_location_zip']) && strlen($_POST['en_daylight_location_zip']) > 4) {
                $get_address = EnDaylightDistance::get_address(sanitize_text_field($_POST['en_daylight_location_zip']), 'address');
                $decoded_address = json_decode($get_address, true);

                if (isset($decoded_address['severity'], $decoded_address['Message']) && $decoded_address['severity'] == 'ERROR') {
                    echo wp_json_encode([
                        'severity' => 'error',
                        'message' => $decoded_address['Message'],
                    ]);
                    exit;
                } elseif (isset($decoded_address['error']) && !empty($decoded_address['error'])) {
                    echo wp_json_encode([
                        'severity' => 'error',
                        'message' => EN_DAYLIGHT_707,
                    ]);
                    exit;
                } elseif (isset($decoded_address['results'], $decoded_address['status']) &&
                    (empty($decoded_address['results'])) &&
                    ($decoded_address['status'] === 'ZERO_RESULTS')) {
                    echo wp_json_encode([
                        'severity' => 'error',
                        'message' => EN_DAYLIGHT_708,
                    ]);
                    exit;
                } elseif (isset($decoded_address['results']) && count($decoded_address['results']) === 0) {
                    echo wp_json_encode([
                        'severity' => 'error',
                        'message' => EN_DAYLIGHT_708,
                    ]);
                    exit;
                }

                $first_city = $country = $city_option = $address_type = $city = $state = '';

                if (count($decoded_address['results']) > 0) {
                    $arr_components = isset($decoded_address['results'][0]['address_components']) ? $decoded_address['results'][0]['address_components'] : [];

                    if (isset($decoded_address['results'][0]['postcode_localities']) && $decoded_address['results'][0]['postcode_localities']) {
                        foreach ($decoded_address['results'][0]['postcode_localities'] as $index => $component) {
                            $first_city = ($index === 0) ? $component : $first_city;
                            $city_option .= '<option value="' . trim($component) . ' "> ' . $component . ' </option>';
                        }

                        $city = '<div class="en_popup_location_input_field">';
                        $city .= '<label for="en_location_city">City</label>';
                        $city .= '<select id="' . $address_type . '_city" class="en_location_input_field en_multi_city_change" name="' . $address_type . '_city" aria-required="true" aria-invalid="false">' . $city_option . '</select>';
                        $city .= '<span class="en_location_error"></span>';
                        $city .= '</div>';

                        $postcode_localities = 1;
                    } elseif ($arr_components) {
                        foreach ($arr_components as $index => $component) {
                            $type = $component['types'][0];
                            if ($city === '' && ($type === 'sublocality_level_1' || $type === 'locality')) {
                                $city_name = trim($component['long_name']);
                            }
                        }
                    }
                    if ($arr_components) {
                        foreach ($arr_components as $index => $state_app) {
                            $type = $state_app['types'][0];
                            if ($state === '' && ($type === 'administrative_area_level_1')) {
                                $state_name = trim($state_app['short_name']);
                                $state = $state_name;
                            }
                            if ($country === '' && ($type === 'country')) {
                                $country_name = trim($state_app['short_name']);
                                $country = $country_name;
                            }
                        }
                    }

                    echo wp_json_encode([
                        'first_city' => $first_city,
                        'city' => $city_name,
                        'city_option' => $city,
                        'state' => $state,
                        'country' => $country,
                        'en_postcode_localities' => $postcode_localities
                    ]);

                    exit;
                }
            }

            echo wp_json_encode(['message' => EN_DAYLIGHT_709]);
            exit;
        }

        /**
         * Delete row from location tab
         */
        public function en_daylight_location_delete_row()
        {
            global $wpdb;
            $en_table = $wpdb->prefix . 'warehouse';
            $en_location_id = (isset($_POST['en_location_id'])) ? sanitize_text_field($_POST['en_location_id']) : "";
            $location = (isset($_POST['en_location_type'])) ? sanitize_text_field($_POST['en_location_type']) : "";
            $wpdb->delete($en_table, array('id' => $en_location_id));

            if ($location === 'warehouse') {
                $en_location_html = EnDaylightWarehouseTemplate::en_load();
                $en_target_location = '.en_location_warehouse_main_div';
            } else if ($location === 'dropship') {
                $en_location_html = EnDaylightDropshipTemplate::en_load();
                $en_target_location = '.en_location_dropship_main_div';
            }

            $message = ucwords($location) . ' deleted successfully.';

            echo wp_json_encode([
                'message' => $message,
                'location' => $location,
                'location_id' => $en_location_id,
                'target_location' => $en_target_location,
                'html' => $en_location_html,
            ]);
            exit;
        }

        /**
         * Location btn clicked
         */
        public function en_daylight_location_save_form_data()
        {
            global $wpdb;
            $post_data = [];
            $action = $location = $message = $en_location_html = $severity = $location_id = $en_target_location = '';
            $en_table = $wpdb->prefix . 'warehouse';

            parse_str($_POST['en_post_data'], $post_data);

            // When checkbox unchecked forcefully mark "no" to serialize data
            (!isset($post_data['enable_store_pickup'])) ? $post_data['enable_store_pickup'] = 'no' : '';
            (!isset($post_data['enable_local_delivery'])) ? $post_data['enable_local_delivery'] = 'no' : '';
            (!isset($post_data['suppress_local_delivery'])) ? $post_data['suppress_local_delivery'] = 'no' : '';

            if (isset($post_data['en_wd_origin_markup'])) {
                $post_data['origin_markup'] = $post_data['en_wd_origin_markup'];
            }
            
            unset($post_data['en_wd_origin_markup']);

            $duplicate_post_data = $post_data;

            if (isset($post_data['id'], $post_data['location'])) {
                unset($post_data['id']);

                $location = $post_data['location'];

                if ($location === 'warehouse') {
                    $location_step = 'Warehouse';
                    $en_location_template_obj = new EnDaylightWarehouseTemplate();
                    $en_target_location = '.en_location_warehouse_main_div';
                    $validate = ['zip', 'city', 'state', 'country', 'location'];
                } else {
                    $location_step = 'Dropship';
                    $en_location_template_obj = new EnDaylightDropshipTemplate();
                    $en_target_location = '.en_location_dropship_main_div';
                    $validate = ['nickname', 'zip', 'city', 'state', 'country', 'location'];
                }

                $en_flipped_data = array_flip($validate);
                $en_intersected_data = array_intersect_key($post_data, $en_flipped_data);

                $en_location_data = EnDaylightWarehouse::get_data($en_intersected_data);

                $severity = 'success';
                $location_id = $duplicate_post_data['id'];

                if (strlen($duplicate_post_data['id']) > 0 &&
                    (empty($en_location_data) ||
                        (!empty($en_location_data) &&
                            reset($en_location_data)['id'] === $location_id))) {
                    $message = $location_step . ' updated successfully.';
                    $action = 'update';
                    $wpdb->update($en_table, $post_data, array('id' => $location_id));
                } elseif (empty($en_location_data)) {
                    $message = $location_step . ' added successfully.';
                    $action = 'insert';
                    $wpdb->insert($en_table, $post_data);
                } else {
                    $message = EN_DAYLIGHT_710;
                    $severity = 'error';
                }

                $en_location_html = $en_location_template_obj::en_load();
            }

            echo wp_json_encode([
                'severity' => $severity,
                'message' => $message,
                'action' => $action,
                'location' => $location,
                'location_id' => $location_id,
                'target_location' => $en_target_location,
                'html' => $en_location_html,
            ]);

            exit;
        }

    }

}