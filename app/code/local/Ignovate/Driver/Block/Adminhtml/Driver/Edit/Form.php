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
            'class' => 'validate-number validate-length maximum-length-10',
        ));

        $fieldset->addField('aadhaar_id', 'text', array(
            'name'      => 'aadhaar_id',
            'label'     => $helper->__('Aadhaar ID'),
            'title'     => $helper->__('Aadhaar ID'),
            'required' => true,
            'class' => 'validate-alphanum validate-length maximum-length-12'
        ));
        $fieldset->addField('file_aadhaar', 'file', array(
            'label'     => $helper->__('Attachment'),
            'required'  => true,
            'name'      => 'file_aadhaar'
        ));

        $fieldset->addField('pan_number', 'text', array(
            'name'      => 'pan_number',
            'label'     => $helper->__('Pan No'),
            'title'     => $helper->__('Pan No'),
            'class'     => 'validate-alphanum validate-length maximum-length-15',
            'required'  => true
        ));
        $fieldset->addField('file_pan', 'file', array(
            'label'     => $helper->__('Attachment'),
            'required'  => true,
            'name'      => 'file_pan'
        ));

        $fieldset->addField('driving_license', 'text', array(
            'name'      => 'driving_license',
            'label'     => $helper->__('Driving License'),
            'title'     => $helper->__('Driving License'),
            'class'     => 'validate-alphanum validate-length maximum-length-15',
            'required'  => true
        ));
        $fieldset->addField('file_license', 'file', array(
            'label'     => $helper->__('Attachment'),
            'required'  => true,
            'name'      => 'file_license'
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

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('adminhtml')->__('Store View'),
                'title'     => Mage::helper('adminhtml')->__('Store View'),
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $driver->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($driver && $driver->getId()) {
            $form->setValues($driver->getData());
            $form->setDataObject($driver);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}