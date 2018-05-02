<?php

class Ignovate_Mobile_Model_Api2_Cart_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Cart_Abstract
{
    public function _create($request)
    {
        try {
            /** @var Mage_Checkout_Model_Cart $cart */
            $cart = Mage::getSingleton('checkout/cart');
            foreach ($request['product'] as $productId => $qty)
            {
                Mage::getSingleton("core/session", array("name" => "frontend"));
                $cart->addProduct($productId, array('qty' => $qty));
            }
            $cart->save();

            $cart1 = Mage::getSingleton('checkout/session');
            $cart1->setCartWasUpdated(true);
            $result_array["quoteid"] = $quoteid=$cart1->getQuoteId();
            $result_array["items_count"]  =Mage::helper('checkout/cart')->getCart()->getItemsCount();

            //Get Customer details
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer')->load($request['customer_id']);

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
            $subtotal=Mage::helper('directory')->currencyConvert($base_subtotal, $base_currency_code , $currency_code);
            $base_subtotal_with_discount=$quote->getBaseSubtotalWithDiscount();
            $subtotal_with_discount=Mage::helper('directory')->currencyConvert($base_subtotal_with_discount, $base_currency_code , $currency_code);

            $quote->setGrandTotal($grand_total)->setSubtotal($subtotal)->setSubtotalWithDiscount($subtotal_with_discount);
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

