<?php

/**
 * @var $this \WPStaging\Backend\Pro\Notices\Notices
 * @var $viewsNoticesPath
 * @see \WPStaging\Backend\Pro\Notices\Notices::messages
 */

use WPStaging\Backend\Notices\Notices;
use WPStaging\Framework\Facades\Escape;

?>
<div class="notice notice-warning wpstg-backups-diff-prefix-notice">
    <p>
        <strong><?php esc_html_e('WP STAGING - Please create a new backup.', 'wp-staging'); ?></strong> <br/>
        <?php echo sprintf(
            Escape::escapeHtml(__('A backup created with previous version WP STAGING 4.0.2 can lead to login issues if the backup is restored on another host. If possible, create a new backup and delete the existing ones.<br>In case you need to keep your existing backup and are going to migrate it to another server, <a href="%s" target="_blank">read this article</a>.', 'wp-staging')),
            'https://wp-staging.com/docs/can-not-login-after-restoring-backup/'
        ); ?>
    </p>
    <p>
      <?php Notices::renderNoticeDismissAction(
          $viewsNoticesPath,
          'backups_diff_prefix',
          '.wpstg_dismiss_backups_diff_prefix_notice',
          '.wpstg-backups-diff-prefix-notice'
      ) ?>
    </p>
</div>
