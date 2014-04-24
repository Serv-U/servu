<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('servu_blogproducts')};
CREATE TABLE {$installer->getTable('servu_blogproducts')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    blog_id INT ( 11 ) NOT NULL,
    product_id INT ( 11 ) NOT NULL,
    position int(11) NOT NULL default 0,
  INDEX ( id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>