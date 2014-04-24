<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD recovered TINYINT(1); 
");

$installer->endSetup();
?>