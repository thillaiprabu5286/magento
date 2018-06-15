<?php

class Ignovate_Driver_Model_Api2_Home_Rest_Admin_V2
    extends Ignovate_Driver_Model_Api2_Home_Abstract
{
    public function _retrieveCollection()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        if (empty($quoteId)) {
            Mage::throwException('Quote Id is not specified');
        }

        $storeId = $this->getRequest()->getParam('store_id');
        if (empty($storeId)) {
            Mage::throwException('Store Id is not specified');
        }

        $response = array();
        try {
            /** @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getModel('sales/quote')
                ->setStoreId($storeId)
                ->load($quoteId);
            $response = $this->_buildQuote($quote);

        } catch (Exception $e) {
            echo (string)$e->getMessage();
        }

        return $response;
    }
}

