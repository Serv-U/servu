<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_review_mailed')};
CREATE TABLE {$installer->getTable('sd_review_mailed')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT( 11 ) NOT NULL,
    store_id INT( 11 ) NOT NULL,
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    customer_email TEXT,
    coupon_code TEXT,
    email_status TINYINT( 4 ) UNSIGNED,
  INDEX ( id , order_id, store_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>