<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_manager_manufacturer')}` ADD banner TEXT AFTER description;
");

$installer->endSetup();
?>