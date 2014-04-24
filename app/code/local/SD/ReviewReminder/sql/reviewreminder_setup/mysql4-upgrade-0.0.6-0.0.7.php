<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD recovered_on TIMESTAMP AFTER recovered;
");

$installer->endSetup();
?>