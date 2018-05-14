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

                //Send sms after order creation
                /** @var Ignovate_Sms_Helper_Data $helper */
                $helper = Mage::helper('ignovate_sms');
                $helper->sendSms($order, 'NewOrderNew');

                $this->_getSession()->clear();

                $params = array ('id' => $order->getIncrementId());
                $this->_successMessage(
                    'Order placed successfully.',
                    Mage_Api2_Model_Server::HTTP_OK,
                    $params
                );
            } catch (Exception $e){
                $this->_critical(
                    'Order not placed. Please contact veggies8to8 administrator.',
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

    public function _retrieve()
    {
        $orderId = $this->getRequest()->getParam('id');
        if (empty($orderId)) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        try {

            $order = Mage::getModel('sales/order')->load($orderId);

            return $this->_buildOrderData($order);

        } catch (Exception $e) {
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * Prepare Order Response Data
     *
     * @param $order
     * @return array
     */
    protected function _buildOrderData($order)
    {
        $orderData = array (
            'order_number' => $order->getIncrementId(),
            'grand_total' => $order->getGrandTotal(),
            'ordered_date' => $order->getCreatedAt(),
            'status_label' => Mage::helper('core')->__($order->getStatusLabel()),
            'tax_amount'    => $order->getTaxAmount()
        );

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $orderData['customer'] = array (
            'customer_id' => $customer->getId(),
            'customer_email' => $customer->getEmail(),
            'name'  => $customer->getFirstname() . ' ' . $customer->getLastname()
        );

        $itemData = array();
        foreach ($order->getAllVisibleItems() as $item) {

            if ($item->getProductType() == 'configurable') {
                continue;
            }

            $childItems = array(
                'product_id' => $item->getProductId(),
                'sku' => $item->getSku(),
                'name'  => $item->getName(),
                'product_type' => $item->getProductType(),
                'price' => $item->getPrice(),
                'qty_ordered' => $item->getQtyOrdered()
            );

            $itemData[] = $childItems;
        }
        $orderData['items'] = $itemData;

        // Order Address details
        $address = $order->getBillingAddress();
        if ($address && $address->getId()) {
            $orderData['billing'] = $address->getData();
        }

        $address = $order->getShippingAddress();
        if ($address && $address->getId()) {
            $orderData['shipping'] = $address->getData();
        }

        $orderData['shipping_method'] = array (
            'value' => $order->getShippingAmount(),
            'code' => $order->getShippingMethod(),
            'label' => $order->getShippingDescription()
        );

        $payment = $order->getPayment();
        if ($payment && $payment->getId()) {
            $method = $payment->getMethod();
            $paymentTitle = Mage::getStoreConfig('payment/'.$method.'/title');
            $orderData['payment_method'] = array (
                'code' => $payment->getMethod(),
                'label' => $paymentTitle
            );
        }

        return $orderData;
    }
}

