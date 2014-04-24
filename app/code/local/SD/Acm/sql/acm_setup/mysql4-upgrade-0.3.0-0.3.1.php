<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD ordered BOOLEAN AFTER recovered_date;
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD initial_cart_amount DECIMAL(12,4) AFTER ordered;    
");

$installer->endSetup();

?>