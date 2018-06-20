<?php

class Ignovate_Driver_Model_Api2_Home_Rest_Admin_V2
    extends Ignovate_Driver_Model_Api2_Home_Abstract
{
    public function _retrieveCollection()
    {
        try {

            $driverId = $this->getRequest()->getParam('id');
            if (empty($driverId)) {
                Mage::throwException('Driver id is empty');
            }

            //Get Sales order collection from Driver id
            /** @var  $order */
            $order = Mage::getResourceModel('sales/order_collection');
            $collection = $order->addFieldToFilter(
                    'driver',
                    $driverId
                )
                ->addFieldToFilter(
                    'status',
                    'driver_assigned'
                );

            $arr = array ();
            if ($collection->getSize() > 0) {

                foreach ($collection as $data) {

                    //Get Store Name
                    $store = Mage::app()->getStore($data->getStoreId())->getName();

                    //Get Phonenumber
                    $shippingAddress = $data->getShippingAddress();
                    $phone = $shippingAddress->getTelephone();

                    $arr[] = array (
                        'id' => $data->getId(),
                        'order_number' => $data->getIncrementId(),
                        'store_name' => $store,
                        'customer_name' => $shippingAddress->getFirstname(),
                        'phone' => $phone,
                        'status' => $data->getStatus(),
                        'driver_status' => $data->getDriverStatus()
                    );
                }
            }

            return $arr;

        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_OK
            );
        }
    }
}

