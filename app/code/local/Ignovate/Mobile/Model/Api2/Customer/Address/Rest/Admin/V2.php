<?php

class Ignovate_Mobile_Model_Api2_Customer_Address_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Customer_Address_Abstract
{
    /**
     * POST address
     *
     * @throws Exception
     * @throws Mage_Api2_Exception
     * @param array $requestData
     * @return array
     */
    public function _create($request)
    {
        $consumer = Mage::getModel('oauth/consumer');
        if (empty($request['api_key'])) {
            Mage::throwException('Consumer key is not specified');
        }

        $consumer->load($request['api_key'], 'key');
        if (!$consumer->getId()) {
            Mage::throwException('Consumer key is incorrect');
        }

        $customer_id = $this->getRequest()->getParam('customer_id');
        if (empty($customer_id)) {
            Mage::throwException('Customer is not specified');
        }

        if (empty($request['door_no'])
            || empty($request['street_name'])
        ) {
            Mage::throwException('Address field missing');
        }

        if (empty($request['city_id'])) {
            Mage::throwException('Store ID missing');
        }

        $customer = $this->_loadCustomerById(
            $this->getRequest()->getParam('customer_id')
        );

        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('customer/address');

        try {

            //Merge Extra Address fields
            $ext = array (
                $request['door_no'],
                $request['apt_name'],
                $request['street_name'],
                $request['landmark']
            );
            $ext = array_filter($ext);
            $extra = implode(',', $ext);

            $address->setData($request);
            $address->setStreet($extra);

            $address->setCustomer($customer);
            if (isset($requestData['is_default'])) {
                $address->setIsDefaultBilling($request['is_default']);
                $address->setIsDefaultShipping($request['is_default']);
            }

            $validate = $address->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('customer')->__('Attribute "%s" is required.', $code));
                    }
                    else {
                        Mage::throwException($error);
                    }
                }
            }
            $address->save();

            $this->_successMessage(
                'Address successfully created',
                Mage_Api2_Model_Server::HTTP_OK,
                array(
                    'customer_id' => $address->getCustomerId(),
                    'customer_address_id' => $address->getId()
                )
            );

        } catch (Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }
    }

    public function _retrieve()
    {
        $customer_id = $this->getRequest()->getParam('customer_id');
        if (empty($customer_id)) {
            Mage::throwException('Customer is not specified');
        }

        $address_id = $this->getRequest()->getParam('address_id');
        if (empty($address_id)) {
            Mage::throwException('Customer Address is not specified');
        }

        $address = $this->_loadCustomerAddressById(
            $this->getRequest()->getParam('address_id')
        );
        // Check the owner of loaded resource
        if ($this->getRequest()->getParam('customer_id') != $address->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $address;
    }

    public function _retrieveCollection()
    {
        $customer_id = $this->getRequest()->getParam('customer_id');
        if (empty($customer_id)) {
            Mage::throwException('Customer is not specified');
        }

        try {
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $customerAddress = array();
            foreach ($customer->getAddresses() as $address) {
                $customerAddress[] = $address->toArray();
            }

        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_NOT_FOUND
            );
        }

        //$slice = array_slice($customerAddress, -3, 3, true);
        //return array_values($slice);
        return $customerAddress;
    }

    public function _update($request)
    {
        $consumer = Mage::getModel('oauth/consumer');
        if (empty($request['api_key'])) {
            Mage::throwException('Consumer key is not specified');
        }

        $consumer->load($request['api_key'], 'key');
        if (!$consumer->getId()) {
            Mage::throwException('Consumer key is incorrect');
        }

        $customer_id = $this->getRequest()->getParam('customer_id');
        if (empty($customer_id)) {
            Mage::throwException('Customer is not specified');
        }

        $address_id = $this->getRequest()->getParam('address_id');
        if (empty($address_id)) {
            Mage::throwException('Customer Address is not specified');
        }

        $address = $this->_loadCustomerAddressById($address_id);

        $customer = $this->_loadCustomerById($customer_id);

        // Check the owner of loaded resource
        if ($this->getRequest()->getParam('customer_id') != $address->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        try {

            $address->addData($request);

            $validate = $address->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('customer')->__('Attribute "%s" is required.', $code));
                    }
                    else {
                        Mage::throwException($error);
                    }
                }
            }
            $address->save();

            $this->_successMessage(
                'Address successfully updated',
                Mage_Api2_Model_Server::HTTP_OK,
                array(
                    'customer_id' => $address->getCustomerId(),
                    'customer_address_id' => $address->getId()
                )
            );

        } catch (Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }
    }
}
