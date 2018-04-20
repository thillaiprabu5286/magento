<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 04/09/16
 * Time: 12:09 PM
 */
class Ignovate_Driver_Block_Adminhtml_Driver extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'ignovate_driver';
        $this->_controller = 'adminhtml_driver';
        $this->_headerText = Mage::helper('ignovate_driver')->__('Manage Drivers');

        parent::__construct();
    }
}