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

            $storeId = array_shift($data['stores']);
            if ($storeId) {
                $driver->setStoreId($storeId);
            }

            $maxsize    = 2097152;

            if(isset($_FILES['file_aadhaar']['name'])
                && $_FILES['file_aadhaar']['name'] != '')
            {

                if(($_FILES['file_aadhaar']['size'] >= $maxsize) || ($_FILES["file_aadhaar"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_aadhaar');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_aadhaar']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_aadhaar']['name']);
                //this way the name is saved in DB
                $data['file_aadhaar'] = $fileName;
            } else {
                if(isset($data['file_aadhaar']['delete']) && $data['file_aadhaar']['delete'] == 1) {
                    $data['file_aadhaar'] = '';
                } else {
                    unset($data['file_aadhaar']);
                }
            }

            if(isset($_FILES['file_pan']['name'])
                && $_FILES['file_pan']['name'] != '')
            {

                if(($_FILES['file_pan']['size'] >= $maxsize) || ($_FILES["file_pan"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_pan');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_pan']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_pan']['name']);
                //this way the name is saved in DB
                $data['file_pan'] = $fileName;
            } else {
                if(isset($data['file_pan']['delete']) && $data['file_pan']['delete'] == 1) {
                    $data['file_pan'] = '';
                } else {
                    unset($data['file_pan']);
                }
            }

            if(isset($_FILES['file_license']['name'])
                && $_FILES['file_license']['name'] != '')
            {

                if(($_FILES['file_license']['size'] >= $maxsize) || ($_FILES["file_license"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_license');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_license']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_license']['name']);
                //this way the name is saved in DB
                $data['file_license'] = $fileName;
            } else {
                if(isset($data['file_license']['delete']) && $data['file_license']['delete'] == 1) {
                    $data['file_license'] = '';
                } else {
                    unset($data['file_license']);
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