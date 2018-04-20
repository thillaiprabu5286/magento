<?php

class Ignovate_Driver_Block_Adminhtml_Driver_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle(Mage::helper('ignovate_driver')->__('Driver Information'));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Ignovate_Driver_Block_Adminhtml_Driver_Edit_Form
     */
    protected function _prepareForm()
    {
        $driver = Mage::registry('current_driver');
        
        $helper = Mage::helper('ignovate_driver');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('_current' => true)),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('editForm', array(
            'legend'    => $helper->__("General Information"),
        ));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $helper->__('Name'),
            'title'     => $helper->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('phone', 'text', array(
            'name'      => 'phone',
            'label'     => $helper->__('Phone'),
            'title'     => $helper->__('Phone'),
            'required'  => true,
        ));

        $fieldset->addField('aadhaar_id', 'text', array(
            'name'      => 'aadhaar_id',
            'label'     => $helper->__('Aadhaar ID'),
            'title'     => $helper->__('Aadhaar ID'),
        ));

        $fieldset->addField('pan_number', 'text', array(
            'name'      => 'pan_number',
            'label'     => $helper->__('Pan No'),
            'title'     => $helper->__('Pan No'),
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => $helper->__('Active'),
            'title'     => $helper->__('Active'),
            'required'  => false,
            'options'       => array(
                '1'     => 'Yes',
                '0'     => 'No',
            ),
        ));

        if ($driver && $driver->getId()) {
            $form->setValues($driver->getData());
            $form->setDataObject($driver);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}