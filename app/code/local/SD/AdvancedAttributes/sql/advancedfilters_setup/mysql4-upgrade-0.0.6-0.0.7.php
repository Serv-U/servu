<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sd_advancedfilters_filters')}
	DROP COLUMN depend_categories;
");

$installer->endSetup();

?>

