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

            $maxsize    = 2097152;

            if(isset($_FILES['file_1']['name'])
                && $_FILES['file_1']['name'] != '')
            {

                if(($_FILES['file_1']['size'] >= $maxsize) || ($_FILES["file_1"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_1');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_1']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_1']['name']);
                //this way the name is saved in DB
                $data['file_1'] = $fileName;
            } else {
                if(isset($data['file_1']['delete']) && $data['file_1']['delete'] == 1) {
                    $data['file_1'] = '';
                } else {
                    unset($data['file_1']);
                }
            }

            if(isset($_FILES['file_2']['name'])
                && $_FILES['file_2']['name'] != '')
            {

                if(($_FILES['file_2']['size'] >= $maxsize) || ($_FILES["file_2"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_2');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_2']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_2']['name']);
                //this way the name is saved in DB
                $data['file_2'] = $fileName;
            } else {
                if(isset($data['file_2']['delete']) && $data['file_2']['delete'] == 1) {
                    $data['file_2'] = '';
                } else {
                    unset($data['file_2']);
                }
            }

            if(isset($_FILES['file_3']['name'])
                && $_FILES['file_3']['name'] != '')
            {

                if(($_FILES['file_3']['size'] >= $maxsize) || ($_FILES["file_3"]["size"] == 0)) {
                    Mage::throwException('File too large. File must be less than 2MB.');
                }

                /* Starting upload */
                $uploader = new Varien_File_Uploader('file_3');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg','jpeg', 'png', 'pdf', 'doc', 'docx'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'driver' . DS . 'docs';
                $uploader->save($path, $_FILES['file_3']['name'] );

                $fileName = str_replace(" ","_",$_FILES['file_3']['name']);
                //this way the name is saved in DB
                $data['file_3'] = $fileName;
            } else {
                if(isset($data['file_3']['delete']) && $data['file_3']['delete'] == 1) {
                    $data['file_3'] = '';
                } else {
                    unset($data['file_3']);
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