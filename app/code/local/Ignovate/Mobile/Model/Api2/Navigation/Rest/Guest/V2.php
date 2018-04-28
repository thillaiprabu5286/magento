<?php

class Ignovate_Mobile_Model_Api2_Navigation_Rest_Guest_V2
    extends Ignovate_Mobile_Model_Api2_Navigation_Abstract
{
    public function _retrieveCollection()
    {
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
                    'special_price'     => 'product.special_price'
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

        return $indexData;

    }
}