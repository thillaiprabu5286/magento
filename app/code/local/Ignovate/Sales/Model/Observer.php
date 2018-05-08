<?php

class Ignovate_Sales_Model_Observer
{
    // for event sales_order_save_commit_after
    public function triggerCompleteSms($observer)
    {
        $order = $observer->getOrder();
        if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE){
            // send sms
            $helper = Mage::helper('ignovate_sms');
            $helper->sendSms($order, 'OrderClosure');
        }

        return $this;
    }

}