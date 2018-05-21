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
        $debug = true;
        $customerId = $this->getRequest()->getParam('customer_id');
        $items = $this->_getItems(
            $customerId,
            $this->getRequest()->getParam('store_id')
        );
        return $items;
    }

    /**
     * New wishlist item
     */
    protected function _create(array $data)
    {
        $debug = true;
        $customerId = (int)$this->getRequest()->getParam('customer_id');
        $storeId = (int)$this->getRequest()->getParam('store_id');

        $productId  = isset($data['product_id']) ? $data['product_id'] : false;
        try {
            if (!$productId) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }

            /** @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
            $wishlist->save();

            $item = Mage::getModel('wishlist/item');
            $item->setProductId($product->getId())
                ->setWishlistId($wishlist->getId())
                ->setAddedAt(now())
                ->setStoreId($storeId)
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
            'message'   => "Item {$product->getName()} added to wishlist"
        );
    }

    /**
     * Delete wishlist item for customer id
     */
    protected function _delete()
    {
        $debug = true;
        $customerId = $this->getRequest()->getParam('customer_id');
        $productId     = (int)$this->getRequest()->getParam('product_id');
        $storeId     = (int)$this->getRequest()->getParam('store_id');

        $response = array();
        try {
            /** @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer($customerId);
            if (null !== $wishlist->getId()) {
                /** @var Mage_Wishlist_Model_Resource_Item_Collection $collection */
                $collection = Mage::getResourceModel('wishlist/item_collection');
                $collection->addWishlistFilter($wishlist)
                    ->addStoreFilter($storeId)
                    ->setVisibilityFilter();
                $item  = $collection->getItemByColumnValue('product_id', $productId);
                $product = Mage::getModel('catalog/product')->load($productId);
                if ($item) {
                    $item->delete();
                    $response = array(
                        'status' => 'success',
                        'message'   => "Item {$product->getName()} removed from wishlist"
                    );
                }
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message'   => $e->getMessage()
            );
        }

        return $response;
    }

    /**
     * Get wishlist items prepared for rest response
     *
     * @return array
     */
    protected function _getItems($customerId, $storeId)
    {
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId);

        if (null === $wishlist->getId()) {
            return array();
        }

        /** @var Mage_Wishlist_Model_Resource_Item_Collection $collection */
        $collection = Mage::getResourceModel('wishlist/item_collection');
        $collection->addWishlistFilter($wishlist)
            ->addStoreFilter($storeId)
            ->setVisibilityFilter();

        //$collection = $wishlist->getItemCollection();
        $result = array();
        foreach ($collection as $item) {

            //Load product by store
            $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($item->getProductId());

            $price = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            $result[] = array(
                'item_id' => $item->getId(),
                'product_id' => $item->getProductId(),
                'name' => $product->getName(),
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