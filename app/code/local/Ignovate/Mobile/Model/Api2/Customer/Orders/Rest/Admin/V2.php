<?php

class Ignovate_Mobile_Model_Api2_Customer_Orders_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Customer_Orders_Abstract
{

    public function _retrieve()
    {
        $customer = $this->_loadCustomerById(
            $this->getRequest()->getParam('id')
        );

        return $this->buildCustomerOrderObj(
            $customer,
            $this->getRequest()->getParam('store_id')
        );
    }
}