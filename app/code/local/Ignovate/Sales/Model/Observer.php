<?php

class Ignovate_Sales_Model_Observer
{
    // for event sales_order_save_commit_after
    public function triggerCompleteSms($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getStatus() == 'pending') {
            /** @var Ignovate_Sms_Helper_Data $helper */
            $helper = Mage::helper('ignovate_sms');
            $helper->sendSms($order, 'NewOrderNew');
        } else {
            /** @var Ignovate_Sms_Helper_Fcm $helper */
            $helper = Mage::helper('ignovate_sms/fcm');
            $helper->sendSms($order);

        }

        return $this;
    }
}