<?php

class Ignovate_Mobile_Model_Api2_Customer_Rest_Guest_V2
    extends Ignovate_Mobile_Model_Api2_Customer_Abstract
{

    public function _create($request)
    {
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

            // Initialize empty customer model
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(self::DEFAULT_CITY)
                ->setStoreId(self::DEFAULT_STORE)
                ->setId(null);
            // Initialize customer group
            $customer->getGroupId();

            if ($request['source'] == 'direct') {
                $email = $request['key'] . '@veggies.com';
            } else {
                $email = $request['key'];
            }
            $customer->setEmail($email);
            $customer->setPassword($request['key']);
            $customer->setPasswordConfirmation($request['key']);

            // NOTE: preset of first and last name is temporal
            if (!$customer->getFirstname()) {
                $customer->setFirstname('FirstName');
            }
            if (!$customer->getLastname()) {
                $customer->setLastname('LastName');
            }

            // Validate customer model
            $customerErrors = $customer->validate();
            if (true !== $customerErrors) {
                foreach ($customerErrors as $error) {
                    $this->_error($error, Mage_Api2_Model_Server::HTTP_OK);
                }
                Mage::throwException('Customer data is invalid');
            }

            // Save customer
            $customer->save();

            // Generate token for new created customer
            /** @var Ignovate_Oauth_Model_Token $token */
            $token = Mage::getModel('oauth/token');
            $token->setConsumerId($consumer->getId());
            $token->createFromCustomer($customer);
            $token->save();

            return array(
                'token_id'          => $token->getEntityId(),
                'customer_id'       => $customer->getId(),
                'token'             => $token->getToken(),
                'secret'            => $token->getSecret(),
            );
        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_OK
            );
        }
    }
}