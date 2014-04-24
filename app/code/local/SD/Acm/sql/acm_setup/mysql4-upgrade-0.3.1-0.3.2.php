<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD abandoned_date TIMESTAMP AFTER recovered_date;    
");

$installer->endSetup();

?>