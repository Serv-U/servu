<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD yesnoContacted BOOLEAN DEFAULT 0 AFTER ordered;
ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD comments TEXT AFTER ordered;
");


$installer->endSetup();

?>