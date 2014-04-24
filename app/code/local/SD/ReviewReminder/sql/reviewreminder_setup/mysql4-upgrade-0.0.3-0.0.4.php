<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_review_mailed')}` ADD coupon_order_id INT(11);
");

$installer->endSetup();
?>