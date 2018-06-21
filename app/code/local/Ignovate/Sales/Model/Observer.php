<?php

class Ignovate_Sales_Model_Observer
{
    // for event sales_order_save_commit_after
    public function triggerCompleteSms($observer)
    {
        $order = $observer->getEvent()->getOrder();

        /** @var Ignovate_Sms_Helper_Fcm $helper */
        $helper = Mage::helper('ignovate_sms/fcm');
        $helper->sendSms($order);

        return $this;
    }
}