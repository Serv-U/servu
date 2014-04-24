<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE  `servu_mediamanager_files` ADD  `date_modified` TIMESTAMP NULL;
	ALTER TABLE  `servu_mediamanager_files` ADD  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
");

$installer->endSetup();
?>