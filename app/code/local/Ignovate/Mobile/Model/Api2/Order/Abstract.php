<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 08/11/17
 * Time: 12:41 PM
 */
class Ignovate_Mobile_Model_Api2_Order_Abstract extends Ignovate_Api2_Model_Resource
{
    /**
     * Initialize order creation session data
     *
     * @param $data
     * @return $this
     */
    protected function _initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->_getSession()->setCustomerId((int) $data['customer_id']);
        }

        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->_getSession()->setStoreId((int) $data['store_id']);
        }

        /* Get/identify store */
        if (!empty($data['quote_id'])) {
            $this->_getSession()->setQuoteId((int) $data['quote_id']);
        }

        return $this;
    }

    /**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve Product Model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::getModel('catalog/product');
    }
}