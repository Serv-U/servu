<?php
$installer = $this;
$installer->startSetup();
    
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` MODIFY recovered_on TIMESTAMP NULL DEFAULT NULL;
");

$installer->endSetup();
?>