<?php

$installer = $this;

$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_advancedfilters_filters')};
CREATE TABLE {$installer->getTable('sd_advancedfilters_filters')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    attribute_id INT( 11 ) NOT NULL,
    attribute_code VARCHAR( 255 ) NOT NULL,
    display_type TINYINT( 4 ) UNSIGNED,
    unfolded_options SMALLINT( 6 ) UNSIGNED,
    is_collapsed BOOLEAN,
    show_on_list BOOLEAN,
    show_on_product BOOLEAN,
    no_follow_tag BOOLEAN,
    no_index_tag BOOLEAN,
    rel_no_follow BOOLEAN,
    single_choice BOOLEAN,
    dependant_options VARCHAR ( 255 ),
    exempt_categories VARCHAR ( 255 ),
    tool_tip VARCHAR( 255 ),
  INDEX ( id ,attribute_id, attribute_code)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('sd_advancedfilters_options')};
CREATE TABLE {$installer->getTable('sd_advancedfilters_options')} (
    id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    option_id INT( 11 ) NOT NULL,
    is_featured BOOLEAN,
    title VARCHAR( 255 ),
    description TEXT,
    cms_block VARCHAR ( 255 ),
    title_tag VARCHAR ( 255 ),
    meta_tag VARCHAR ( 255 ),
    product_list_image VARCHAR ( 255 ),
    product_view_image VARCHAR ( 255 ),
    layered_image VARCHAR ( 255 ),
  INDEX ( id ,option_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();

?>