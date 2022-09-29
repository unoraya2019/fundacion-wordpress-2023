<?php

/**
 * @see WPStaging\Backend\Administrator::getLicensePage
 *
 * @var object $license
 */

use WPStaging\Framework\Facades\Sanitize;

?>
<div class="wpstg_admin">
    <?php require_once(WPSTG_PLUGIN_DIR . 'Backend/views/_main/header.php'); ?>

    <label for="wpstg_license_key" style='display:block;margin-bottom: 5px;margin-top:10px;'><?php echo sprintf(esc_html__('Enter your license key to activate WP STAGING | PRO. %s You can buy a license key on %s.', 'wp-staging'), '<br>', '<a href="https://wp-staging.com?utm_source=wpstg-license-ui&utm_medium=website&utm_campaign=enter-license-key&utm_id=purchase-key&utm_content=wpstaging" target="_blank">wp-staging.com</a>'); ?></label>
      <form method="post" action="#">

      <input type="text" name="wpstg_license_key" style="width:260px;" value='<?php echo esc_attr(get_option('wpstg_license_key', '')); ?>'>
      <?php

        if (isset($license->error) && $license->error === 'expired') {
            $message =  '<span class="wpstg--red">' . __('Your license expired on ', 'wp-staging') . date_i18n(get_option('date_format'), strtotime($license->expires, current_time('timestamp'))) . '</span>';
        } elseif (isset($license->license) && $license->license === 'valid') {
            $message =  __('You\'ll get updates and support until ', 'wp-staging') . date_i18n(get_option('date_format'), strtotime($license->expires, current_time('timestamp')));
            $message .= '<p><a href="' . esc_url(admin_url()) . 'admin.php?page=wpstg_clone" id="wpstg-new-clone" class="wpstg-next-step-link wpstg-link-btn button-primary">' . __("Start using WP STAGING", "wp-staging") . '</a>';
        } else {
            $message = '';
        }

        wp_nonce_field('wpstg_license_nonce', 'wpstg_license_nonce');
        if (isset($license->license) && $license->license === 'valid') {
            echo '<input type="hidden" name="wpstg_deactivate_license" value="1">';
            echo '<input type="submit" class="button" value="' . esc_html__('Deactivate License', 'wp-staging') . '">';
        } else {
            echo '<input type="hidden" name="wpstg_activate_license" value="1">';
            echo '<input type="submit" class="wpstg-button wpstg-blue-primary" value="' . esc_html__('Activate License', 'wp-staging') . '">';
        }
        ?>
        </form>
        <?php echo '<div style="padding:3px;">' . wp_kses_post($message) . '</div>'; ?>
</div>
