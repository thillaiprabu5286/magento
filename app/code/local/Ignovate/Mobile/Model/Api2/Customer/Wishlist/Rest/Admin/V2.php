<?php

class Ignovate_Mobile_Model_Api2_Customer_Wishlist_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Customer_Wishlist_Abstract
{
    /**
     * Retrieve all wishlist items for customer id
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');

        $items = $this->_getItems(
            $customerId
        );

        return $items;
    }

    /**
     * New wishlist item
     */
    protected function _create(array $data)
    {
        $customerId = (int)$this->getRequest()->getParam('customer_id');
        $productId  = isset($data['product_id']) ? $data['product_id'] : false;
        try {
            if (!$productId) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
            $newWishlist = false;
            // save wishlist if it is new
            if (null === $wishlist->getId()) {
                $newWishlist = true;
                $wishlist->save();
            }
            // check existance of product in customer wishlist
            $isValid = true;
            if (!$newWishlist) {
                foreach ($wishlist->getItemCollection() as $item) {
                    if ($item->getProductId() == $product->getId()) {
                        $this->_critical('product exist in wishlist');
                        break;
                    }
                }
            }
            $item = Mage::getModel('wishlist/item');
            $item->setProductId($product->getId())
                ->setWishlistId($wishlist->getId())
                ->setAddedAt(now())
                ->setStoreId($product->getStoreId())
                ->setQty(1)
                ->save();
        } catch (Exception $e) {

            return array(
                'status' => 'error',
                'message'   => $e->getMessage()
            );
        }

        return array(
            'status' => "success",
            'message'   => "Item {$product->getName()} added success"
        );
    }

    /**
     * Delete wishlist item for customer id
     */
    protected function _delete()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $productId     = (int)$this->getRequest()->getParam('product_id');

        try {
            /** @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer($customerId);
            if (null !== $wishlist->getId()) {
                $items = $wishlist->getItemCollection();
                $item  = $items->getItemByColumnValue('product_id', $productId);
                if ($item) {
                    $item->delete();
                    return array('success' => true);
                }
            }
            $this->_critical(self::RESOURCE_NOT_FOUND);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Get wishlist items prepared for rest response
     *
     * @return array
     */
    protected function _getItems($customerId, $itemId = null)
    {
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId);

        if (null === $wishlist->getId()) {
            return array();
        }

        $collection = $wishlist->getItemCollection();

        if (null !== $itemId) {
            $collection->getSelect()->where('wishlist_item_id = ?', (int)$itemId);
        }

        $result = array();
        foreach ($collection as $item) {

            $product = $item->setStoreId($this->getStoreId())
                ->setLang($this->getLang())
                ->getProduct();

            $price = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();

            $result[] = array(
                'item_id' => $item->getId(),
                'product_id' => $item->getProductId(),
                'name' => $product->getName(),
                'stock_status' => $product->getStockItem()->getIsInStock(),
                'price' => $price,
                'special_price' => $specialPrice,
                'image_url' => $product->getImageUrl(),
                'sku' => $product->getSku(),
            );
        }

        return $result;
    }

    /**
     * Check if request customer id match api user
     */
    /*protected function _isOwner($customerId)
    {
        if ($this->getApiUser()->getUserId() !== $customerId) {
            return false;
        }
        return true;
    }*/

}