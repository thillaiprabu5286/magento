<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 08/11/17
 * Time: 12:41 PM
 */
class Ignovate_Mobile_Model_Api2_Customer_Address_Abstract extends Ignovate_Api2_Model_Resource
{
    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }

    /**
     * Load customer address by id
     *
     * @param int $id
     * @return Mage_Customer_Model_Address
     */
    protected function _loadCustomerAddressById($id)
    {
        $address = Mage::getModel('customer/address')->load($id);

        //Add extra fields
        $address['city_id'] = $address->getCityId();

        // Throw error if delete flag is on
        if (!$address->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $address;
    }

}