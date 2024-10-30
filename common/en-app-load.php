<?php

/**
 * App Name load classes.
 */

namespace EnDaylightLoad;

use EnDaylightCsvExport\EnDaylightCsvExport;
use EnDaylightConfig\EnDaylightConfig;
use EnDaylightCreateLTLClass\EnDaylightCreateLTLClass;
use EnDaylightLocationAjax\EnDaylightLocationAjax;
use EnDaylightMessage\EnDaylightMessage;
use EnDaylightOrderRates\EnDaylightOrderRates;
use EnDaylightOrderScript\EnDaylightOrderScript;
use EnDaylightPlans\EnDaylightPlans;
use EnDaylightWarehouse\EnDaylightWarehouse;
use EnDaylightTestConnection\EnDaylightTestConnection;
use EnDaylightOrderWidget\EnDaylightOrderWidget;

/**
 * Load classes.
 * Class EnDaylightLoad
 * @package EnDaylightLoad
 */
if (!class_exists('EnDaylightLoad')) {

    class EnDaylightLoad
    {

        /**
         * Load classes of App Name plugin
         */
        static public function Load()
        {
            new EnDaylightMessage();
            new EnDaylightPlans();
            EnDaylightConfig::do_config();
            new \EnDaylightCarrierShippingRates();
            

            if (is_admin()) {
                new EnDaylightCreateLTLClass();
                new EnDaylightWarehouse();
                new EnDaylightTestConnection();
                new EnDaylightLocationAjax();
                new EnDaylightOrderRates();
                new EnDaylightOrderScript();
                !class_exists('EnOrderWidget') ? new EnDaylightOrderWidget() : '';
                !class_exists('EnCsvExport') ? new EnDaylightCsvExport() : '';
            }
        }

    }
}
