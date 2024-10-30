<?php
/**
 * User guide page.
 */

namespace EnDaylightUserGuide;

/**
 * User guide add detail.
 * Class EnDaylightUserGuide
 * @package EnDaylightUserGuide
 */
if (!class_exists('EnDaylightUserGuide')) {

    class EnDaylightUserGuide
    {

        /**
         * User Guide template.
         */
        static public function en_load()
        {
            ?>
            <div class="en_user_guide">
            <p>
                <?php _e('The User Guide for this application is maintained on the publishers website. To view it click <a href="' . esc_url(EN_DAYLIGHT_DOCUMENTATION_URL) . '" target="_blank">here</a> or paste the following link into your browser.', 'eniture-technology'); ?>
            </p>
            <?php
            echo esc_url(EN_DAYLIGHT_DOCUMENTATION_URL);
        }

    }

}