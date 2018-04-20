<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 17/09/16
 * Time: 5:54 PM
 */
class Ignovate_Driver_Model_Driver extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ignovate_driver/driver');
    }
}