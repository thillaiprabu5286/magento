<?php

/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 04/09/16
 * Time: 3:41 PM
 */
class Ignovate_Driver_Model_Resource_Driver_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ignovate_driver/driver');
    }

    public function toOptionDriver($valueField='id', $labelField='name')
    {
        $res = array();
        foreach ($this as $item) {
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }
}