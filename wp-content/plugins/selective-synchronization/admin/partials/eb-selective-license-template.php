<?php

//Get License key
$license_key = get_option('edd_' . $this->plugin_slug .'_license_key');

//Get License Status
$status = get_option('edd_' . $this->plugin_slug . '_license_status');

$sites = get_option('eb_' . $this->plugin_slug .'_license_key_sites');

$renew_link = get_option('eb_'.$this->plugin_slug.'_product_site');

include_once(dirname(dirname(plugin_dir_path(__FILE__))).'/includes/class-eb-select-get-plugin-data.php');

$active_site = EBSelectGetPluginData::getSiteList($this->plugin_slug);

$display="";

if (!empty($active_site)||$active_site!="") {
    $display = "<ul>".$active_site."</ul>";
}


?>
<div class="wrap">
        <?php

        $license_key = trim(get_option('edd_' . $this->plugin_slug .'_license_key'));
        
        //Handle Submission of inputs on license page
        if (isset($_POST['edd_' . $this->plugin_slug .'_license_key']) && empty($_POST['edd_' . $this->plugin_slug .'_license_key'])) {
                //If empty, show error message
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Please enter license key.', 'selective_synchronization'),
                    'error'
                );
        } else if ($status !== false && $status == 'valid') { //Valid license key
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Your license key is activated.', 'selective_synchronization'),
                    'updated'
                );
        } else if ($status !== false && $status == 'expired' && (!empty($display)||$display != "")) { //Expired license key
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Your license key has Expired. Please, Renew it. <br/>Your License Key is already activated at : '.$display, 'selective_synchronization'),
                    'error'
                );
        } else if ($status !== false && $status == 'expired') { //Expired license key
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Your license key has Expired. Please, Renew it.', 'selective_synchronization'),
                    'error'
                );
        } else if ($status !== false && $status == 'disabled') { //Disabled license key
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Your license key is Disabled.', 'selective_synchronization'),
                    'error'
                );
        } else if ($status == 'invalid' && (!empty($display)||$display != "")) { //Invalid license key   and site
            add_settings_error(
                'eb_' . $this->plugin_slug .'_errors',
                esc_attr('settings_updated'),
                __('Your License Key is already activated at : '.$display, 'selective_synchronization'),
                'error'
            );
        } else if ($status == 'invalid') { //Invalid license key
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Please enter valid license key.', 'selective_synchronization'),
                    'error'
                );
        } else if ($status == 'site_inactive' && (!empty($display)||$display != "")) { //Invalid license key   and site inactive
            add_settings_error(
                'eb_' . $this->plugin_slug .'_errors',
                esc_attr('settings_updated'),
                __('Your License Key is already activated at : '.$display, 'selective_synchronization'),
                'error'
            );
        } else if ($status == 'site_inactive') { //Site is inactive
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('Site inactive(Press Activate license to activate plugin)', 'selective_synchronization'),
                    'error'
                );
        } else if ($status == 'deactivated') { //Site is inactive
                add_settings_error(
                    'eb_' . $this->plugin_slug .'_errors',
                    esc_attr('settings_updated'),
                    __('License Key is deactivated', 'selective_synchronization'),
                    'updated'
                );
        }

        settings_errors('eb_' . $this->plugin_slug .'_errors');

        ?>
        <h2><?php _e($this->plugin_name .' License Options', 'selective_synchronization'); ?></h2>
 
        <form method="post" action="">
                <table class="form-table">
                        <tbody>
                                <tr valign="top">   
                                        <th scope="row" valign="top">
                                            <?php _e('License Key', 'selective_synchronization'); ?>
                                        </th>
                                        <td>
                                            <?php
                                            if (($status=="valid"||$status=="expired")&& (empty($display)||$display == "")) {
                                            ?>
                                                    <input id="<?php echo 'edd_' . $this->plugin_slug .'_license_key'?>" name="<?php echo 'edd_' . $this->plugin_slug .'_license_key'?>" type="text" class="regular-text" value="<?php esc_attr_e($license_key); ?>" readonly/>
                                                    <?php
                                            } else {?>
                                                        <input id="<?php echo 'edd_' . $this->plugin_slug .'_license_key'?>" name="<?php echo 'edd_' . $this->plugin_slug .'_license_key'?>" type="text" class="regular-text" value="<?php esc_attr_e($license_key); ?>" />
                                                    <?php
                                            }
    
                                            ?>
                                            
                                                <label class="description" for="<?php echo 'edd_' . $this->plugin_slug .'_license_key'?>"></label>
                                        </td>
                                </tr>

                                <tr>
                                        <th scope="row" valign="top">
                                                <?php _e('License Status'); ?>
                                        </th>
                                        <td>
                                                <?php if ($status !== false && $status == 'valid') { ?>
                                                        <span style="color:green;"><?php _e('Active', 'selective_synchronization'); ?></span>
                                                <?php
} else if (get_option('edd_' . $this->plugin_slug .'_license_status') == 'site_inactive') {
                                                        ?>
                                                        <span style="color:red;"><?php _e('Not Active', 'selective_synchronization') ?></span>
                                                <?php
} else if (get_option('edd_' . $this->plugin_slug .'_license_status') == 'expired' && (!empty($display)||$display != "")) {
                                                ?>
                                                        <span style="color:red;"><?php  _e('Not Active', 'selective_synchronization') ?></span>
                                                <?php
} else if (get_option('edd_' . $this->plugin_slug .'_license_status') == 'expired') {
                                                        ?>
                                                        <span style="color:green;"><?php  _e('Active', 'selective_synchronization') ?></span>
                                                <?php
} elseif (get_option('edd_' . $this->plugin_slug .'_license_status') == 'invalid') { ?>
                                                        <span style="color:red;"><?php _e('Not Active', 'selective_synchronization'); ?></span>
                                                        <?php

} else {
        ?>
        <span style="color:red;"><?php _e('Not Active', 'selective_synchronization'); ?></span>
                                                <?php
}

                                                ?>
                                        </td>
                                </tr>
                                
                                
                                
                                <tr valign="top">   
                                        <th scope="row" valign="top">
                                                <?php _e('Activate License', 'selective_synchronization'); ?>
                                        </th>
                                        <td>
                                        <?php if ($status !== false && $status == 'valid') { ?>

                                            <?php wp_nonce_field('edd_' . $this->plugin_slug .'_nonce', 'edd_' . $this->plugin_slug .'_nonce'); ?>
                                            <input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_deactivate'; ?>" value="<?php _e('Deactivate License', 'selective_synchronization'); ?>"/>
                                            <?php
} elseif ($status == 'expired' &&(!empty($display)||$display != "")) { ?>

                                            <?php wp_nonce_field('edd_' . $this->plugin_slug .'_nonce', 'edd_' . $this->plugin_slug .'_nonce'); ?>
                                            <input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_activate'; ?>" 
                                                   value="<?php _e('Activate License', 'selective_synchronization'); ?>"/>
                                            <input type="button" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_renew'; ?>" 
                                                   value="<?php _e('Renew License', 'selective_synchronization'); ?>" onclick="window.open('<?php echo $renew_link; ?>')"/> 
                                            <?php
} elseif ($status == 'expired') { ?>
                                            <?php wp_nonce_field('edd_' . $this->plugin_slug .'_nonce', 'edd_' . $this->plugin_slug .'_nonce'); ?>
                                            <input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_deactivate'; ?>"
                                                   value="<?php _e('Deactivate License', 'selective_synchronization'); ?>"/>
                                             <input type="button" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_renew'; ?>"
                                                    value="<?php _e('Renew License', 'selective_synchronization'); ?>" onclick="window.open('<?php echo $renew_link; ?>')"/>
                                            
                                            <?php

} else {
    wp_nonce_field('edd_' . $this->plugin_slug .'_nonce', 'edd_' . $this->plugin_slug .'_nonce');

?>
<input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->plugin_slug .'_license_activate'; ?>" value="<?php _e('Activate License', 'selective_synchronization'); ?>"/>
                                        <?php
} ?>
                                        </td>
                                </tr>
                        </tbody>
                </table>
        </form>
</div>
