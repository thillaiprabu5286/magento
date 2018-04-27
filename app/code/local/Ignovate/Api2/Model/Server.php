<?php

class Ignovate_Api2_Model_Server extends Mage_Api2_Model_Server
{
    /**
     * Authenticate user
     *
     * @throws Exception
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('api2/auth');
        $this->_setAuthUser($authManager->authenticate($request));
        if ($platform = $this->getPlatform()) {
            Mage::register('api_platform', $platform, true, true);
        }

        $this->saveConsumerIdInSession();

        return $this->_getAuthUser();
    }

    protected function saveConsumerIdInSession()
    {
        $consumerKey = $this->getConsumerKey();
        $consumer = Mage::getModel('oauth/consumer');
        $consumer->load($consumerKey, 'key');

        if ($consumer->getId()) {
            $session = Mage::getSingleton('customer/session');
            $session->setConsumerId($consumer->getId());
        }
    }

    protected function getConsumerKey()
    {
        $oauth = $_SERVER['HTTP_AUTHORIZATION'];
        $oauth = str_replace('OAuth ', '', $oauth);
        $oauth = str_replace('"', '', $oauth);
        $values = explode(',', $oauth);
        $params = array();
        foreach ($values as $value) {
            $value = explode('=', $value);
            $params[$value[0]] = $value[1];
        }

        if (isset( $params['oauth_consumer_key'])) {

            return $params['oauth_consumer_key'];
        }

        return false;
    }
}
