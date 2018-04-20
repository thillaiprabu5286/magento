<?php

class Ignovate_Driver_Block_Adminhtml_Driver_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'ignovate_driver';
        $this->_controller = 'adminhtml_driver';

        parent::__construct();
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $driver = Mage::registry('current_driver');
        if ($driver) {
            return Mage::helper('ignovate_driver')->__("Edit Driver " . $driver->getName());
        }

        return Mage::helper('ignovate_driver')->__("New Driver");
    }
}
