<?php

class Aero_Catalogrequest_Model_Mysql4_Catalogrequest extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the catalogrequest_id refers to the key field in your database table.
        $this->_init('catalogrequest/catalogrequest', 'catalogrequest_id');
    }
}