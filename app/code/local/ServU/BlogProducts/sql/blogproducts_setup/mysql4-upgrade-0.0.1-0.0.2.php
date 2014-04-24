<?php
$installer = $this;
$installer->startSetup();

$installer->run("CREATE TABLE {$installer->getTable('servu_productblogs')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT ( 11 ) NOT NULL,
    blog_id INT ( 11 ) NOT NULL,
  INDEX ( id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->run("CREATE TABLE {$installer->getTable('servu_blog_banners')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    blog_id INT ( 11 ) NOT NULL,
    banner TEXT,
  INDEX ( id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();
?>