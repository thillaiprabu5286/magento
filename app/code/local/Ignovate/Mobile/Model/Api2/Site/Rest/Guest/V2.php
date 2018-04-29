<?php

class Ignovate_Mobile_Model_Api2_Site_Rest_Guest_V2
    extends Ignovate_Mobile_Model_Api2_Site_Abstract
{
    public function _retrieveCollection()
    {
        try {
            $response = array();
            foreach (Mage::app()->getWebsites() as $website)
            {
                //Skip backend websites
                if ($website->getCode() == 'admin'
                    || $website->getCode() == 'base'
                ) {
                    continue;
                }

                //Build Website Params
                $countries[] = array (
                    'cityName' => $website->getName(),
                    'city_code' => $website->getCode(),
                    'active' => $this->_checkSingleStoreActive($website->getGroups())
                );

                $response['data'] = $countries;
            }

            return $response;

        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }

    protected function _checkSingleStoreActive($info)
    {
        foreach ($info as $group)
        {
            $stores = $group->getStores();
            foreach ($stores as $store)
            {
                if ($store->getIsActive() == 1) {
                    return 1;
                }
            }
        }
        return 0;
    }
}