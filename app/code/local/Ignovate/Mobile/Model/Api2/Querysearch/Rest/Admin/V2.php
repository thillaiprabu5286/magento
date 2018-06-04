<?php

class Ignovate_Mobile_Model_Api2_Querysearch_Rest_Admin_V2
    extends Ignovate_Mobile_Model_Api2_Querysearch_Abstract
{
    public function _create($request)
    {
        // Validate if consumer key is set in request and if it exists
        $consumer = Mage::getModel('oauth/consumer');
        if (empty($request['api_key'])) {
            Mage::throwException('Consumer key is not specified');
        }
        $consumer->load($request['api_key'], 'key');
        if (!$consumer->getId()) {
            Mage::throwException('Consumer key is incorrect');
        }

        if (empty($request['store_id'])) {
            Mage::throwException('Store id not specified');
        }

        try {

            $customerId = $request['customer_id'];
            $storeId = $request['store_id'];
            $term = $request['term'];

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
                        'special_price'     => 'product.special_price',
                        'units' => 'product.units',
                        'package' => 'product.package'
                    )
                );

            $collectionSelect->joinLeft(
                array ('cat' => 'catalog_category_product'),
                'cat.product_id = product.entity_id'
            );

            $collectionSelect->where(
                "product.name like '%{$term}%'"
            );

            $str = (string)$collectionSelect;

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

        } catch (Mage_Core_Exception $e) {
            throw new Mage_Api2_Exception(
                $e->getMessage(),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }
}

