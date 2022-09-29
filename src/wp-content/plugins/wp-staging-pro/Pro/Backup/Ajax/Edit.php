<?php

// TODO PHP7.x; declare(strict_type=1);
// TODO PHP7.x; type hints & return types

namespace WPStaging\Pro\Backup\Ajax;

use WPStaging\Framework\Component\AbstractTemplateComponent;
use WPStaging\Framework\Filesystem\FileObject;
use WPStaging\Framework\TemplateEngine\TemplateEngine;
use WPStaging\Framework\Utils\Sanitize;
use WPStaging\Pro\Backup\Service\BackupMetadataEditor;
use WPStaging\Pro\Backup\Service\BackupsFinder;
use WPStaging\Pro\Backup\Entity\BackupMetadata;

class Edit extends AbstractTemplateComponent
{
    private $backupMetadataEditor;
    private $backupsFinder;

    /** @var Sanitize */
    private $sanitize;

    public function __construct(BackupsFinder $backupsFinder, BackupMetadataEditor $backupMetadataEditor, Sanitize $sanitize, TemplateEngine $templateEngine)
    {
        parent::__construct($templateEngine);
        $this->backupsFinder        = $backupsFinder;
        $this->backupMetadataEditor = $backupMetadataEditor;
        $this->sanitize             = $sanitize;
    }

    public function render()
    {
        if (!$this->canRenderAjax()) {
            return;
        }

        $md5 = sanitize_text_field(isset($_POST['md5']) ? $_POST['md5'] : '');

        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : 'Backup';

        $name = substr(sanitize_text_field(html_entity_decode($name)), 0, 100);
        $name = str_replace('\\\'', '\'', $name);

        $notes = isset($_POST['notes']) ? $this->sanitize->sanitizeTextareaField($_POST['notes']) : '';
        $notes = substr($notes, 0, 1000);

        if (strlen($md5) !== 32) {
            wp_send_json([
                'error'   => true,
                'message' => __('Invalid request.', 'wp-staging'),
            ]);
        }

        $backups = $this->backupsFinder->findBackups();

        // Early bail: No backups found, nothing to edit
        if (empty($backups)) {
            wp_send_json([
                'error'   => true,
                'message' => __('No backups found, nothing to edit.', 'wp-staging'),
            ]);
        }

        // Name must not be empty.
        if (empty($name)) {
            $name = __('Backup', 'wp-staging');
        }

        /** @var \SplFileInfo $backup */
        foreach ($backups as $backup) {
            if ($md5 === md5($backup->getBasename())) {
                try {
                    $file = new FileObject($backup->getPathname(), FileObject::MODE_APPEND_AND_READ);
                    $metaData = (new BackupMetadata())->hydrateByFile($file);
                    $metaData->setName($name);
                    $metaData->setNote($notes);

                    $this->backupMetadataEditor->setBackupMetadata($file, $metaData);
                } catch (\Exception $e) {
                    wp_send_json([
                        'error'   => true,
                        'message' => esc_html__($e->getMessage(), 'wp-staging'),
                    ]);
                }
            }
        }

        wp_send_json(true);
    }
}
