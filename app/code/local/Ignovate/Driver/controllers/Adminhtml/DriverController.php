<?php

class Ignovate_Driver_Adminhtml_DriverController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initDriver();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $debug = true;
        $driver = $this->_initDriver();
        try {

            $data = $this->getRequest()->getParams();

            if(isset($_FILES['filename']['name'])
                && $_FILES['filename']['name'] != '')
            {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('filename');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['filename']['name'] );

                $fileName = str_replace(" ","_",$_FILES['filename']['name']);
                //this way the name is saved in DB
                $data['filename'] = $fileName;
            } else {
                if(isset($data['filename']['delete']) && $data['filename']['delete'] == 1) {
                    $data['filename'] = '';
                } else {
                    unset($data['filename']);
                }
            }

            if ($driver->getId()) {
                $data['updated_at'] = date ('Y-m-d H:i:s');
            } else {
                $data['created_at'] = date ('Y-m-d H:i:s');
            }
            $driver->addData($data)->save();

            $this->_getSession()->addSuccess(
                $this->__("Driver saved")
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Mage::getModel('ignovate_driver/driver')->load($id);
            $model->delete();
            $this->_getSession()->addSuccess(
                $this->__("Driver deleted")
            );
            $this->_redirect('*/*/');
        }
    }

    protected function _initDriver()
    {
        $driver = Mage::getModel('ignovate_driver/driver');
        if ($id = $this->getRequest()->getParam('id')) {
            $driver->load($id);
            if ($driver->getId()) {
                Mage::register('current_driver', $driver);
            }
        }

        return $driver;
    }

    public function exportSuppliersCsvAction()
    {
        $fileName = 'drivers.csv';
        $grid = $this->getLayout()->createBlock('ignovate_driver/adminhtml_driver_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportSuppliersExcelAction()
    {
        $fileName = 'drivers.xml';
        $grid = $this->getLayout()->createBlock('ignovate_driver/adminhtml_driver_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ignovate_driver');
    }
}