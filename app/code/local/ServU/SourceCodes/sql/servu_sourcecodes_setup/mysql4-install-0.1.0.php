<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn('sales_flat_order', 'sourcecode', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 5,
        'comment' => 'Source Codes'
    )
);
        
$installer->endSetup();
?>