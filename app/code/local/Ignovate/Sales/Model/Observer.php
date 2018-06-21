<?php

class Ignovate_Sales_Model_Observer
{
    // for event sales_order_save_commit_after
    public function triggerCompleteSms($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getStatus() == Mage_Sales_Model_Order::STATE_COMPLETE) {
            /** @var Ignovate_Sms_Helper_Fcm $helper */
            $helper = Mage::helper('ignovate_sms/fcm');
            $helper->sendSms($order);

        }

        return $this;
    }
}