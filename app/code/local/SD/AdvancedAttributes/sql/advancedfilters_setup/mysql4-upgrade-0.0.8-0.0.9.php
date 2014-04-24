<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_advancedfilters_configurables_options')};
CREATE TABLE {$installer->getTable('sd_advancedfilters_configurables_options')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    option_id INT( 11 ) NOT NULL,
    option_label VARCHAR( 255 ) NOT NULL,
    product_view_image VARCHAR ( 255 ),
    product_view_thumbnail VARCHAR ( 255 ),
  INDEX ( id ,option_id, option_label)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
ALTER TABLE sd_advancedfilters_configurables DROP product_view_image;
ALTER TABLE sd_advancedfilters_configurables DROP product_view_thumbnail;
");


$installer->endSetup();

?>

