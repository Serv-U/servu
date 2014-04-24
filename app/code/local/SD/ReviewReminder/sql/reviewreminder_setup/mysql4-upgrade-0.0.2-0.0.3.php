<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_review_emails')};
CREATE TABLE {$installer->getTable('sd_review_emails')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    review_mailed_id INT( 11 ) NOT NULL,
    order_id INT( 11 ) NOT NULL,
    mailed_at TIMESTAMP NOT NULL,
    email_number TINYINT( 4 ) UNSIGNED,
  INDEX ( id , review_mailed_id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>