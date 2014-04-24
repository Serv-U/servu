<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'exported', array('type'=>'boolean', 'default'=>0));
$installer->run("
    ALTER TABLE `{$this->getTable('sales/order_grid')}` ADD COLUMN exported BOOLEAN DEFAULT 0;");
    
$installer->endSetup();
?>