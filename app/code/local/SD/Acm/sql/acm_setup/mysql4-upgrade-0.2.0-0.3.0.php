<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_carts_sent')};
CREATE TABLE {$installer->getTable('sd_carts_sent')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cart_mailed_id INT( 11 ) NOT NULL,
    quote_id INT( 11 ) NOT NULL,
    mailed_at TIMESTAMP NOT NULL,
    email_number TINYINT( 4 ) UNSIGNED,
  INDEX ( id , cart_mailed_id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD has_recovered BOOLEAN AFTER is_active;
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD recovered_date TIMESTAMP NULL DEFAULT NULL AFTER has_recovered;
");

$installer->endSetup();

?>