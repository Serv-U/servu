<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sd_advancedfilters_filters')}
	ADD `depend_categories` varchar(255) AFTER `exempt_categories` ;
");

$installer->endSetup();

?>

