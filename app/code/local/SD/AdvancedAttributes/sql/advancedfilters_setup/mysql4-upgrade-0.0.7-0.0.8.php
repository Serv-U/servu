<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_advancedfilters_configurables')};
CREATE TABLE {$installer->getTable('sd_advancedfilters_configurables')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    attribute_id INT( 11 ) NOT NULL,
    attribute_code VARCHAR( 255 ) NOT NULL,
  INDEX ( id ,attribute_id, attribute_code)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");


$installer->endSetup();

?>

