<?php

/**
 * All App Name messages
 */

namespace EnDaylightMessage;

/**
 * Messages are relate to errors, warnings, headings
 * Class EnDaylightMessage
 * @package EnDaylightMessage
 */
if (!class_exists('EnDaylightMessage')) {

    class EnDaylightMessage
    {

        /**
         * Add all messages
         * EnDaylightMessage constructor.
         */
        public function __construct()
        {
            if (!defined('EN_DAYLIGHT_ROOT_URL')) {
                define('EN_DAYLIGHT_ROOT_URL', esc_url('https://eniture.com'));
            }
            define('EN_DAYLIGHT_STANDARD_PLAN_URL', EN_DAYLIGHT_ROOT_URL . '/plan/woocommerce-daylight-ltl-freight/');
            define('EN_DAYLIGHT_ADVANCED_PLAN_URL', EN_DAYLIGHT_ROOT_URL . '/plan/woocommerce-daylight-ltl-freight/');
            define('EN_DAYLIGHT_SUBSCRIBE_PLAN_URL', EN_DAYLIGHT_ROOT_URL . '/plan/woocommerce-daylight-ltl-freight/');
            define('EN_DAYLIGHT_700', "You are currently on the Trial Plan. Your plan will be expire on ");
            define('EN_DAYLIGHT_701', "You are currently on the Basic Plan. The plan renews on ");
            define('EN_DAYLIGHT_702', "You are currently on the Standard Plan. The plan renews on ");
            define('EN_DAYLIGHT_703', "You are currently on the Advanced Plan. The plan renews on ");
            define('EN_DAYLIGHT_704', "Your currently plan subscription is inactive <a href='javascript:void(0)' data-action='en_daylight_get_current_plan' onclick='en_update_plan(this);'>Click here</a> to check the subscription status. If the subscription status remains 
                inactive. Please activate your plan subscription from <a target='_blank' href='" . EN_DAYLIGHT_SUBSCRIBE_PLAN_URL . "'>here</a>");

            define('EN_DAYLIGHT_705', "<a target='_blank' class='en_plan_notification' href='" . EN_DAYLIGHT_STANDARD_PLAN_URL . "'>
                        Standard Plan required
                    </a>");
            define('EN_DAYLIGHT_706', "<a target='_blank' class='en_plan_notification' href='" . EN_DAYLIGHT_ADVANCED_PLAN_URL . "'>
                        Advanced Plan required
                    </a>");
            define('EN_DAYLIGHT_707', "Please verify credentials at connection settings panel.");
            define('EN_DAYLIGHT_708', "Please enter valid US or Canada zip code.");
            define('EN_DAYLIGHT_709', "Success! The test resulted in a successful connection.");
            define('EN_DAYLIGHT_710', "Zip code already exists.");
            define('EN_DAYLIGHT_711', "Connection settings are missing.");
            define('EN_DAYLIGHT_712', "Shipping parameters are not correct.");
            define('EN_DAYLIGHT_713', "Origin address is missing.");
            define('EN_DAYLIGHT_714', ' <a href="javascript:void(0)" data-action="en_daylight_get_current_plan" onclick="en_update_plan(this);">Click here</a> to refresh the plan');
        }

    }

}