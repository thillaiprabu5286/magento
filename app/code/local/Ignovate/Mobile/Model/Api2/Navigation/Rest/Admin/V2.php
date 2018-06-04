<?php

class Ignovate_Mobile_Model_Api2_Navigation_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Navigation_Abstract
{
    public function _retrieveCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');

        $storeCode = $this->getRequest()->getParam('store');
        $storeId = Mage::app()->getStore($storeCode)->getId();

        $categoryId = $this->getRequest()->getParam('category');

        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('product' => 'catalog_product_flat_' . $storeId),
                array(
                    'id'                => 'product.url_key',
                    'name'              => 'product.name',
                    'thumbnail'         => 'product.thumbnail',
                    'small_image'       => 'product.small_image',
                    'url_path'          => 'product.url_path',
                    'url_key'           => 'product.url_key',
                    'sku'               => 'product.sku',
                    'price'             => 'product.price',
                    'special_price'     => 'product.special_price',
                    'units' => 'product.units',
                    'package' => 'product.package'
                )
            );

        $collectionSelect->join(
            array('category' => 'catalog_category_product'),
            'product.entity_id = category.product_id'
        );

        $collectionSelect->where(
            'category.category_id = ?', $categoryId
        );

        $indexData = $readAdapter->query($collectionSelect)->fetchAll();

        $final = array();
        foreach ($indexData as $key => $data) {
            $id = $data['product_id'];
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
            $collection = Mage::getModel('wishlist/item')->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('wishlist_id', $wishlist->getId())
                ->addFieldToFilter('product_id', $id);
            $item = $collection->getFirstItem();
            $isWishlist = 0;
            if ($item->getId()) {
                $isWishlist = 1;
            }
            $data['is_wishlist'] = $isWishlist;

            $final[] = $data;
        }

        return $final;
    }
}