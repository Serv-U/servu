<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD created_at TIMESTAMP AFTER quote_id;
    ALTER TABLE `{$this->getTable('sd_carts_mailed')}` ADD updated_at TIMESTAMP AFTER created_at;
");

$installer->endSetup();
?>