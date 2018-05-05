<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 08/11/17
 * Time: 12:41 PM
 */
class Ignovate_Mobile_Model_Api2_Customer_Orders_Abstract extends Ignovate_Api2_Model_Resource
{
    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id, $storeId)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->setStoreId($storeId)->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }

    public function buildCustomerOrderObj($customer, $storeId)
    {
        // Get Customer Log
        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('order' => 'sales_flat_order_grid'),
                array(
                    'order_id' => 'order.entity_id',
                    'increment_id' => 'order.increment_id',
                    'name'  => 'order.shipping_name',
                    'total' => 'order.grand_total'
                )
            );

        $collectionSelect->joinLeft(
                array('statuses' => 'sales_order_status'),
                'statuses.status = order.status',
                array('status' => 'statuses.label')
            )
            ->joinLeft(
                array('store' => 'core_store'),
                'store.store_id = order.store_id',
                array('area' => 'store.name')
            );

        $collectionSelect
            ->where(
                'order.customer_id = ?', $customer->getId()
            )
            ->where(
                'order.store_id = ?', $storeId
            )
            ->order(
                'order.created_at DESC'
            );

        $indexData = $readAdapter->query($collectionSelect)->fetchAll();

        return $indexData;
    }
}