<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('servu_shipping_confirmation_number')}`;
    CREATE TABLE `{$this->getTable('servu_shipping_confirmation_number_quote')}` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `quote_id` int(11) unsigned NOT NULL,
        `key` varchar(255) NOT NULL,
        `value` text NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE `{$this->getTable('servu_shipping_confirmation_number_order')}` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `order_id` int(11) unsigned NOT NULL,
        `key` varchar(255) NOT NULL,
        `value` text NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
?>