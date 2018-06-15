<?php

class Ignovate_Driver_Model_Api2_Order_Rest_Admin_V2
    extends Ignovate_Driver_Model_Api2_Order_Abstract
{
    /**
     * Change Order Status by Order Entity ID
     *
     * @param array $params
     * @return $this
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function _update(array $params)
    {
        $order = $this->_loadOrderById(
            $this->getRequest()->getParam('id')
        );

        if (!$order->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        if (!isset($params['driver_id'])) {
            $this->_critical('Driver ID required');
        }

        if (!isset($params['status'])) {
            return $this;
        }

        $comment = '';
        if (!empty($params['comment'])) {
            $comment = $params['comment'];
        }

        try {

            $driver = Mage::getModel('ignovate_driver/driver')->load($params['driver_id']);
            $driverName = $driver->getName();
            $pieces = array ($comment, $driverName);
            $parts = join (" - ", $pieces);
            $order->addStatusHistoryComment($parts);

            $order->setDriverStatus($params['status']);
            $order->save();

            Mage::dispatchEvent('sales_order_status_update', array(
                'order' => $order,
            ));

            $this->_successMessage(
                'Order updated successfully',
                Mage_Api2_Model_Server::HTTP_OK,
                $this->_getParams($order)
            );
        } catch (Exception $e) {
            // Catch any type of exception and convert it into API2 exception
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }

    protected function _getParams($order)
    {
        $message = array (
            'order_number' => $order->getIncrementId(),
        );

        return $message;
    }

    protected function _loadOrderById($id)
    {
        $order = Mage::getModel('sales/order')->load($id);
        return $order;
    }
}

