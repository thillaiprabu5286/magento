<?php

class Ignovate_Sales_Model_Observer
{
    // for event sales_order_save_commit_after
    public function triggerCompleteSms($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE){
            // send sms
            /** @var Ignovate_Sms_Helper_Fcm $helper */
            $helper = Mage::helper('ignovate_sms/fcm');
            $helper->sendSms($order);
        }

        return $this;
    }

    public function onSaveDriverChangeStatus($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if($order->getState() == 'ready_for_delivery') {
            if (!empty($order->getDriver()) && $order->getDriver() != '') {
                //Modify order status
                $order->setStatus('driver_assigned');
            }
        }

    }
}