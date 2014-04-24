<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('servu_shipping_confirmation_number'))
    ->addColumn('confirmation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Confirmation Id')
    ->addColumn('rate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Rate Id')
    ->addColumn('confirmation_number', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Confirmation Number')
    ->addForeignKey(
        $installer->getFkName(
            'servu_shipping_confirmation_number',
            'rate_id',
            'sales/quote_address_shipping_rate',
            'rate_id'
        ),
        'rate_id', $installer->getTable('sales/quote_address_shipping_rate'), 'rate_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Servu Shipping Confirmation Number');
$installer->getConnection()->createTable($table);

$installer->endSetup();
?>