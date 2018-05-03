<?php

class Ignovate_Mobile_Model_Api2_Site_Rest_Admin_V2
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
                    'id' => $website->getId(),
                    'name' => $website->getName(),
                    'code' => $website->getCode(),
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

    public function _retrieve()
    {
        $cityId = $this->getRequest()->getParam('id');
        if (empty($cityId)) {
            $this->_critical(self::RESOURCE_DATA_INVALID);
        }

        //Get Stores by Website
        $response = array();
        $website = Mage::getModel('core/website')->load($cityId);
        foreach ($website->getGroups() as $group) {
            $stores = $group->getStores();
            $areas = array();
            foreach ($stores as $store) {
                $areas[] = array (
                    'id' => $store->getStoreId(),
                    'name' => $store->getName(),
                    'code' => $store->getCode(),
                    'active' => $store->getIsActive()
                );
            }
            $response['data'] = $areas;
        }
        return $response;
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