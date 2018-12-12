<?php
/**
 * Type: updated,error,update-nag
 */
if (!function_exists('softsdev_notice')) {

    function softsdev_notice($message, $type)
    {
        $html = <<<EOD
<div class="{$type} notice">
<p>{$message}</p>
</div>
EOD;
        echo $html;
    }

}

function softsdev_wpp_activation_notice() {
	require_once dirname( __FILE__ ).'/../templates/premium-activation-notice.phtml';
}
