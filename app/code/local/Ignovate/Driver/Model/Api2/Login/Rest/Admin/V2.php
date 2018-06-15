<?php

class Ignovate_Driver_Model_Api2_Login_Rest_Admin_V2
    extends Ignovate_Driver_Model_Api2_Login_Abstract
{
    public function _create($request)
    {
        $debug = true;
        try {

            // Validate if consumer key is set in request and if it exists
            $consumer = Mage::getModel('oauth/consumer');
            if (empty($request['api_key'])) {
                Mage::throwException('Consumer key is not specified');
            }
            $consumer->load($request['api_key'], 'key');
            if (!$consumer->getId()) {
                Mage::throwException('Consumer key is incorrect');
            }

            $identifier = $request['phone'];
            if (empty($identifier)) {
                Mage::throwException('Invalid data');
            }

            /** @var Ignovate_Driver_Model_Resource_Driver_Collection $collection */
            $collection = Mage::getResourceModel('ignovate_driver/driver_collection');
            $data = $collection->addFieldToFilter('phone', $identifier)
                ->getFirstItem();
            if (is_object($data) && $data->getId()) {
                return array (
                    'status' => 'success',
                    'data' => array (
                        'id' => $data->getId(),
                        'name' => $data->getName(),
                    )
                );
            } else {
                return array (
                    'status' => 'error',
                    'data' => array ()
                );
            }

        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_OK
            );
        }
    }
}

