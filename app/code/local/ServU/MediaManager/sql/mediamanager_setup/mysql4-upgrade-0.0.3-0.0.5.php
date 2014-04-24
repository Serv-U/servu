<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE  `servu_mediamanager_files` ADD  `file_manufacturer_date` DATE NULL;
	ALTER TABLE  `servu_mediamanager_files` ADD  `no_follow` BOOLEAN DEFAULT 1;
");

$installer->endSetup();
?>