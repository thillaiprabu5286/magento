<?php

class Ignovate_Mobile_Model_Api2_Customer_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Customer_Abstract
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

            if ($request['source'] == 'direct') {
                $email = $request['key'] . '@veggies.com';
            } else {
                $email = $request['key'];
            }

            // Generate token for new created customer
            /** @var Ignovate_Oauth_Model_Token $token */
            $token = Mage::getModel('oauth/token');
            $token->setConsumerId($consumer->getId());

            $customer = Mage::getModel('customer/customer');
            $customer->setWebsiteId(self::DEFAULT_CITY)
                ->setStoreId(self::DEFAULT_STORE);
            $customer->loadByEmail($email);
            if (is_object($customer) && $customer->getId()) {
                $token->loadByCustomer($customer);
            } else {
                // Initialize empty customer model
                $newCustomer = Mage::getModel('customer/customer')
                    ->setWebsiteId(self::DEFAULT_CITY)
                    ->setStoreId(self::DEFAULT_STORE)
                    ->setId(null);

                // Initialize customer group
                $newCustomer->getGroupId();
                $newCustomer->setEmail($email);
                $newCustomer->setPassword($request['key']);
                $newCustomer->setPasswordConfirmation($request['key']);
                $newCustomer->setFcmId($request['fcm_id']);

                // NOTE: preset of first and last name is temporal
                if (empty($request['name'])) {
                    $newCustomer->setFirstname('Name');
                } else {
                    $newCustomer->setFirstname($request['name']);
                }

                if (!$newCustomer->getLastname()) {
                    $newCustomer->setLastname('.');
                }

                // Validate customer model
                $customerErrors = $newCustomer->validate();
                if (true !== $customerErrors) {
                    foreach ($customerErrors as $error) {
                        $this->_error($error, Mage_Api2_Model_Server::HTTP_OK);
                    }
                    Mage::throwException('Customer data is invalid');
                }

                // Save customer
                $newCustomer->save();

                //Trigger welcome sms
                /** @var Ignovate_Sms_Helper_Data $helper */
                $helper = Mage::helper('ignovate_sms');
                $helper->sendWelomeSms($request['key'], 'Welcome');

                $customer = $newCustomer;

                $token->createFromCustomer($customer);
                $token->save();
            }

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