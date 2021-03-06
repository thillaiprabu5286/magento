<?php
/**
 * API2 Server
 * Overrided for save consumerId in session
 *
 * @category Markavip
 * @package Markavip_Api2
 * @author Dawid Udziela <dawid.udziela@hatimeria.pl>
 */
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

        return $this->_getAuthUser();
    }
}
