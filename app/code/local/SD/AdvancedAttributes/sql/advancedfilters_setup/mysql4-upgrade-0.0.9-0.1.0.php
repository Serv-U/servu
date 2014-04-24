<?php
$installer = $this;
$installer->startSetup();
$installer->run("RENAME TABLE $installer->getTable('sd_advancedfilters_configurables') TO $installer->getTable('sd_advancedattributes_configurables_options'),
             $installer->getTable('sd_advancedfilters_configurables_options') TO $installer->getTable('sd_advancedattributes_configurables_options'),
             $installer->getTable('sd_advancedfilters_filters') TO $installer->getTable('sd_advancedattributes_filters'),
             $installer->getTable('sd_advancedfilters_options') TO $installer->getTable('sd_advancedattributes_options');");
$installer->endSetup();
?>

