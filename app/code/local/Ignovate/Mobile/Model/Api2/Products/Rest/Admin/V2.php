<?php

class Ignovate_Mobile_Model_Api2_Products_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Products_Abstract
{
    public function _retrieveCollection()
    {
        $storeCode = $this->getRequest()->getParam('store');
        $storeId = Mage::app()->getStore($storeCode)->getId();

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

        $indexData = $readAdapter->query($collectionSelect)->fetchAll();

        return $indexData;

    }
}