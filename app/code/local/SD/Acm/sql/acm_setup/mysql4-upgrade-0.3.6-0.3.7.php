<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('sd_acm_unsubscribe')}` ADD is_active BOOLEAN AFTER customer_id;
");

$installer->endSetup();

?>