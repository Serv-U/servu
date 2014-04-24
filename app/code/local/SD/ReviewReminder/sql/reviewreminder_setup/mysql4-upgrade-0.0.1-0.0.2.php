<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD ordered_date TIMESTAMP NULL DEFAULT NULL AFTER updated_at;
");

$installer->endSetup();
?>