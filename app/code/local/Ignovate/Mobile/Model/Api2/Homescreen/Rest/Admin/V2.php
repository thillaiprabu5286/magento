<?php

class Ignovate_Mobile_Model_Api2_Homescreen_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Products_Abstract
{
    public function _retrieveCollection()
    {
        $storeCode = $this->getRequest()->getParam('store');
        $storeId = Mage::app()->getStore($storeCode)->getId();

        $limit = $this->getRequest()->getParam('limit');

        $t1['slide1'] = $this->_topVeggies(3, $storeId, $limit);
        $t2['slide2'] = $this->_topFruits(6, $storeId, $limit);
        $t3['slide3'] = $this->_topSpecials($storeId, $limit);

        $temp = array_merge($t1, $t2, $t3);

        return $temp;
    }

    protected function _topVeggies($id, $storeId, $limit)
    {
        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('product' => 'catalog_product_flat_' . $storeId),
                array(
                    'product_id'        => 'product.entity_id',
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

        $collectionSelect->joinLeft(
            array ('cat' => 'catalog_category_product'),
            'cat.product_id = product.entity_id'
        );

        $collectionSelect
            ->where(
                'cat.category_id = ?', $id
            )
            ->order(
                'RAND()'
            )
            ->limit($limit);

        $response = $readAdapter->query($collectionSelect)->fetchAll();

        return $response;
    }

    protected function _topFruits($id, $storeId, $limit)
    {
        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('product' => 'catalog_product_flat_' . $storeId),
                array(
                    'product_id'        => 'product.entity_id',
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

        $collectionSelect->joinLeft(
            array ('cat' => 'catalog_category_product'),
            'cat.product_id = product.entity_id'
        );

        $collectionSelect
            ->where(
                'cat.category_id = ?', $id
            )
            ->order(
                'RAND()'
            )
            ->limit($limit);

        $response = $readAdapter->query($collectionSelect)->fetchAll();

        return $response;
    }

    protected function _topSpecials($storeId, $limit)
    {
        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('product' => 'catalog_product_flat_' . $storeId),
                array(
                    'product_id'        => 'product.entity_id',
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

        $collectionSelect
            ->where(
                'product.special_price > 0'
            )
            ->order(
                'RAND()'
            )
            ->limit($limit);

        $response = $readAdapter->query($collectionSelect)->fetchAll();

        return $response;
    }
}