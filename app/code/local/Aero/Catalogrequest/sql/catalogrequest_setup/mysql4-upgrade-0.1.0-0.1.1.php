<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('catalogrequest')}`;
CREATE TABLE IF NOT EXISTS {$this->getTable('catalogrequest')} (
  `catalogrequest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL DEFAULT '',
  `mi` char(1) DEFAULT NULL,
  `lname` varchar(50) NOT NULL DEFAULT '',
  `address1` varchar(100) NOT NULL DEFAULT '',
  `address2` varchar(100) DEFAULT NULL,
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(20) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `time_added` datetime NOT NULL,
  `ip` varchar(20) NOT NULL DEFAULT '',
  `hostname` varchar(100) NOT NULL DEFAULT '',
  `heardofus` varchar(200) DEFAULT NULL,
  `product_interest` varchar(200),
  `exist_customer` BOOLEAN,
  `first_catalog` BOOLEAN,
  `res_bus` varchar(200),
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`catalogrequest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

    ");

$installer->endSetup(); 