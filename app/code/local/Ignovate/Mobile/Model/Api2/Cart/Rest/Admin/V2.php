<?php

class Ignovate_Mobile_Model_Api2_Cart_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Cart_Abstract
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

        if (empty($request['customer_id'])) {
            Mage::throwException('Customer Id is not specified');
        }

        if (empty($request['store_id'])) {
            Mage::throwException('Store id not specified');
        }

        //Load Customer by id
        $customer = Mage::getModel('customer/customer')->load($request['customer_id']);
        if(!is_object($customer) || !$customer->getId()){
            Mage::throwException('Invalid Customer id specified');
        }

        if (empty($request['product'])) {
            Mage::throwException('No item specified');
        }

        try {
            /** @var Mage_Checkout_Model_Cart $cart */
            $cart = Mage::getSingleton('checkout/cart');
            foreach ($request['product'] as $productId => $qty)
            {
                //Mage::getSingleton("core/session", array("name" => "frontend"));
                $product = Mage::getModel('catalog/product')
                    ->setStoreId($request['store_id'])
                    ->load($productId);
                $cart->addProduct($product, array('qty' => $qty));
            }
            $cart->save();

            $cart1 = Mage::getSingleton('checkout/session');
            $cart1->setCartWasUpdated(true);
            $result_array["quoteid"] = $quoteid=$cart1->getQuoteId();
            $result_array["items_count"]  =Mage::helper('checkout/cart')->getCart()->getItemsCount();

            //get quote using sales/quote
            $quote = Mage::getModel('sales/quote')->load($quoteid);
            $quote->setStoreId($request['store_id'])
                ->setCustomerId($request['customer_id'])
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerLastname($customer->getLastname());
            $quote->save();

            $store_id = $quote->getStoreId();
            if(isset($store_id) || is_numeric($store_id)){
                $current_currency_code=Mage::app()->getStore($store_id)->getCurrentCurrencyCode();
                $currency_code=$current_currency_code;
            }else{
                $currency_code = Mage::app()->getStore()->getBaseCurrencyCode();
            }
            $base_currency_code=Mage::app()->getStore()->getBaseCurrencyCode();

            $base_grand_total=$quote->getBaseGrandTotal();
            $grand_total=Mage::helper('directory')->currencyConvert($base_grand_total, $base_currency_code , $currency_code);
            $base_subtotal=$quote->getBaseSubtotal();
            $subtotal = Mage::helper('directory')->currencyConvert($base_subtotal, $base_currency_code , $currency_code);
            $base_subtotal_with_discount = $quote->getBaseSubtotalWithDiscount();
            $subtotal_with_discount=Mage::helper('directory')->currencyConvert($base_subtotal_with_discount, $base_currency_code , $currency_code);

            $quote->setGrandTotal($grand_total)
                ->setSubtotal($subtotal)
                ->setSubtotalWithDiscount($subtotal_with_discount);
            $quote->save();

            $response = $this->_buildQuote($quote, $customer);

            return $response;

        } catch (Mage_Core_Exception $e) {
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }
}

