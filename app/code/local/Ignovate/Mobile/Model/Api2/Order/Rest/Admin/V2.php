<?php

class Ignovate_Mobile_Model_Api2_Order_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Order_Abstract
{
    public function _create($request)
    {
        // Validate if consumer key is set in request and if it exists
        $consumer = Mage::getModel('oauth/consumer');
        if (empty($request['api_key'])) {
            Mage::throwException('Consumer key is not specified');
        }
        $consumer->load($request['api_key'], 'key');
        if (!$consumer->getId()) {
            Mage::throwException('Consumer key is incorrect');
        }

        try {

            $this->createOrder($request);

        } catch (Mage_Core_Exception $e) {
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }

    }

    /**
     * Create Sales Order
     *
     * @param $orderData
     */
    public function createOrder($orderData)
    {
        if (!empty($orderData)) {

            $this->_initSession($orderData['session']);

            try {
                
                $this->_processQuote($orderData);

                if (!empty($orderData['payment'])) {
                    $this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
                }

                $order = $this->_getOrderCreateModel()
                    ->importPostData($orderData['order'])
                    ->createOrder();

                $this->_getSession()->clear();

                $params = array ('id' => $order->getIncrementId());
                $this->_successMessage(
                    'Order Created Successfully',
                    Mage_Api2_Model_Server::HTTP_OK,
                    $params
                );
            } catch (Exception $e){
                $this->_critical(
                    Ignovate_Api2_Model_Resource::RESOURCE_REQUEST_DATA_INVALID,
                    Mage_Api2_Model_Server::HTTP_NOT_FOUND
                );
            }
        }
    }

    /**
     * Prepare and Process Quote for Sales Order Creation
     *
     * @param array $data
     * @return $this
     */
    protected function _processQuote($data = array())
    {
        $debug = true;
        /* Saving order data */
        if (!empty($data['order'])) {
            $this->_getOrderCreateModel()->importPostData($data['order']);
        }

        $this->_getOrderCreateModel()->getBillingAddress();
        $this->_getOrderCreateModel()->setShippingAsBilling(true);

        /* Add Product */
        if (!empty($data['items'])) {
            $itemArr = array();
            foreach ($data['items'] as $item) {
                $productId = $this->_getProduct()->getIdBySku($item['sku']);
                $itemArr[$productId] = array ('qty' => $item['qty']);
            }
            $this->_getOrderCreateModel()->addProducts($itemArr);
        }

        /* Collect shipping rates */
        $this->_getOrderCreateModel()->collectShippingRates();

        /* Add payment data */
        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        $this->_getOrderCreateModel()
            ->initRuleData()
            ->saveQuote();

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        return $this;
    }

}

