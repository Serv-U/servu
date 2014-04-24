<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    RENAME TABLE servu_shipping_confirmation_number_quote
        TO servu_shipping_misc_information_quote;
    
    RENAME TABLE servu_shipping_confirmation_number_order
        TO servu_shipping_misc_information_order;
");

$installer->endSetup();
?>