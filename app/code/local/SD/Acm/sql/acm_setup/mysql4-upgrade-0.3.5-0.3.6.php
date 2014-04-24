<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_acm_unsubscribe')};
CREATE TABLE {$installer->getTable('sd_acm_unsubscribe')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    customer_id INT( 11 ) NOT NULL,
    store_id INT( 11 ) NOT NULL,
  INDEX ( id , customer_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>