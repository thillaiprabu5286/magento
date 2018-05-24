<?php

class Ignovate_Mobile_Model_Api2_Site_Quote_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Site_Quote_Abstract
{
    public function _retrieve()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $customerId = $this->getRequest()->getParam('customer_id');

        if (empty($customerId)) {
            $this->_critical(self::RESOURCE_DATA_INVALID);
        }

        //Get Sales quote by customer store
        /** @var Mage_Sales_Model_Resource_Quote_Collection $collection */
        $collection = Mage::getModel('sales/quote')->getCollection();
        $collection->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_active', 1);

        $str = (string)$collection->getSelect();

        if ($collection->getSize() > 0) {
            $data = $collection->getLastItem();
            return array ('quote_id' => $data->getEntityId());
        }

        return array ('quote_id' => null);
    }

}