<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD tracking_url TEXT;
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD customer_name TEXT;
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` MODIFY order_id INT(11) NULL DEFAULT NULL;
    ALTER TABLE `{$this->getTable('sd_review_emails')}` MODIFY order_id INT(11) NULL DEFAULT NULL;    
");

$installer->endSetup();
?>