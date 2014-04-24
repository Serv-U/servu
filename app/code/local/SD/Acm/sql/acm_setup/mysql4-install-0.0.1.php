<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_carts_mailed')};
CREATE TABLE {$installer->getTable('sd_carts_mailed')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    quote_id INT( 11 ) NOT NULL,
    store_id INT( 11 ) NOT NULL,
    is_active BOOLEAN,
    status TINYINT( 4 ) UNSIGNED,
  INDEX ( id , quote_id, store_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>