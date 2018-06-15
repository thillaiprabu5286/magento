<?php

class Ignovate_Driver_Model_Resource_Driver extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ignovate_driver/driver', 'id');
    }
}