<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('mapp')};
    CREATE TABLE {$this->getTable('mapp')} (
    `mapp_id` int(11) unsigned NOT NULL auto_increment,
    `name` varchar(255) NOT NULL default '',
    `emailid` varchar(255) NOT NULL default '',
    `productname` varchar(255) NOT NULL default '',
    `producturl` varchar(255) NOT NULL default '',
    `sku` varchar(255) NOT NULL default '',
    `mapprice` varchar(255) NOT NULL default '',
    `telephone` text NOT NULL default '',
    `status` smallint(6) NOT NULL default '0',
    `created_time` datetime NULL,
    `update_time` datetime NULL,
    `coupon_code` varchar(100) NOT NULL default '',
    `store_id` int(100) NOT NULL default '0',
    PRIMARY KEY (`mapp_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();