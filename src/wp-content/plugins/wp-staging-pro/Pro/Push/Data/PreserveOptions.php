<?php

namespace WPStaging\Pro\Push\Data;

use WPStaging\Framework\Security\AccessToken;
use WPStaging\Framework\SiteInfo;
use WPStaging\Framework\Staging\CloneOptions;
use WPStaging\Framework\Support\ThirdParty\FreemiusScript;

class PreserveOptions extends OptionsTablePushService
{
    /**
     * @inheritDoc
     */
    protected function processOptionsTable()
    {
        $this->log("Preserve Data in " . $this->prodOptionsTable);

        if (!$this->tableExists($this->prodOptionsTable)) {
            return true;
        }

        $sql = '';

        $preserved_option_names = [
            // 'siteurl',
            // 'home',
            'wpstg_optimizer_excluded',
            'wpstg_version_upgraded_from',
            'wpstg_version',
            'wpstg_installDate',
            'wpstg_version_latest',
            'wpstg_queue_table_version',
            'wpstgpro_version_upgraded_from',
            'wpstgpro_version',
            'upload_path',
            AccessToken::OPTION_NAME
        ];

        // Preserve CloneOptions if current site is staging site
        if ((new SiteInfo())->isStagingSite()) {
            $preserved_option_names[] = CloneOptions::WPSTG_CLONE_SETTINGS_KEY;
        }

        $freemiusHelper = new FreemiusScript();
        // Preserve freemius options on the production site if present.
        if ($freemiusHelper->hasFreemiusOptions()) {
            $preserved_option_names = array_merge($preserved_option_names, $freemiusHelper->getFreemiusOptions());
        }

        $preserved_option_names    = apply_filters('wpstg_preserved_options', $preserved_option_names);
        $preserved_options_escaped = esc_sql($preserved_option_names);

        $preserved_options_data = [];

        // Get preserved data in wp_options tables
        $preserved_options_data[$this->tmpOptionsTable] = $this->productionDb->get_results(
            sprintf(
                "SELECT * FROM `$this->prodOptionsTable` WHERE `option_name` IN ('%s')",
                implode("','", $preserved_options_escaped)
            ),
            ARRAY_A
        );

        // Create preserved data queries for options tables
        foreach ($preserved_options_data as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $option) {
                    $sql .= $this->productionDb->prepare(
                        "DELETE FROM `$key` WHERE `option_name` = %s;\n",
                        $option['option_name']
                    );

                    $sql .= $this->productionDb->prepare(
                        "INSERT INTO `$key` ( `option_id`, `option_name`, `option_value`, `autoload` ) VALUES ( NULL , %s, %s, %s );\n",
                        $option['option_name'],
                        $option['option_value'],
                        $option['autoload']
                    );
                }
            }
        }

        $this->debugLog("Preserve values " . json_encode($preserved_options_data));

        $this->executeSql($sql);

        return true;
    }
}
