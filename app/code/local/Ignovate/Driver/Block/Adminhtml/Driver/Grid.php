<?php

class Ignovate_Driver_Block_Adminhtml_Driver_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('driverGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ignovate_driver/driver')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('ignovate_driver');

        $this->addColumn('id',
            array(
                'header'=> $helper->__('Id'),
                'type' => 'text',
                'index' => 'id',
                'width' => '50px'
            ));

        $this->addColumn('name',
            array(
                'header'=> $helper->__('Name'),
                'type' => 'text',
                'index' => 'name'
            ));

        $this->addColumn('phone',
            array(
                'header'=> $helper->__('Phone'),
                'type'  => 'number',
                'index' => 'phone'
            ));

        $this->addColumn('aadhaar_id',
            array(
                'header'=> $helper->__('Aadhaar ID'),
                'type' => 'text',
                'index' => 'aadhaar_id'
            ));

        $this->addColumn('file_aadhaar', array(
            'header'=> $helper->__('Attachment'),
            'filter'=>false,
            'index'=>'file_aadhaar',
            'align' => 'left',
            'width'     => '50px',
            'renderer'  => 'ignovate_driver/adminhtml_driver_grid_renderer_aadhaar',
        ));

        $this->addColumn('pan_number',
            array(
                'header'=> $helper->__('PAN No'),
                'type' => 'text',
                'index' => 'pan_number'
            ));

        $this->addColumn('file_pan', array(
            'header'=> $helper->__('Attachment'),
            'filter'=>false,
            'index'=>'file_pan',
            'align' => 'left',
            'width'     => '50px',
            'renderer'  => 'ignovate_driver/adminhtml_driver_grid_renderer_pan',
        ));

        $this->addColumn('driving_license',
            array(
                'header'=> $helper->__('Driving License'),
                'type' => 'text',
                'index' => 'driving_license'
            ));

        $this->addColumn('file_license', array(
            'header'=> $helper->__('Attachment'),
            'filter'=>false,
            'index'=>'file_license',
            'align' => 'left',
            'width'     => '50px',
            'renderer'  => 'ignovate_driver/adminhtml_driver_grid_renderer_license',
        ));

        $this->addColumn('status', array(
            'header'    => $helper->__('Active'),
            'width'     => '50px',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                0      => $helper->__('No'),
                1      => $helper->__('Yes'),
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  $helper->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => $helper->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }
}