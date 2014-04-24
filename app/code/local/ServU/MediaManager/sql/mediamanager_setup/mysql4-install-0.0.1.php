<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('servu_mediamanager_files')};
CREATE TABLE {$installer->getTable('servu_mediamanager_files')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    file_title VARCHAR( 255 ),
    file_name VARCHAR( 255 ),
    file_extension VARCHAR( 255 ),
    file_size INT( 11 ),
    file_status BOOLEAN,
    file_description TEXT,
  INDEX ( id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('servu_mediamanager_products')};
CREATE TABLE {$installer->getTable('servu_mediamanager_products')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    file_id INT( 11 ) NOT NULL,
    product_id INT( 11 ) NOT NULL,
    position int(11) NOT NULL default 0,
  INDEX ( id )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>