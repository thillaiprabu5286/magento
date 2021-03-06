<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_MultiStoreViewPricing
* @author     Extension Team
* @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
class Bss_MultiStoreViewPricingPriceGroupTierStore_Model_Resource_Product_Attribute_Backend_Groupprice extends Bss_MultiStoreViewPricingPriceGroupTierStore_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('multistoreviewpricingpricegrouptierstore/group_price', 'value_id');
    }

    public function getPriceStoreData($productId, $storeId = null, $customerGroup = null) {
        $adapter = $this->_getReadAdapter();

        $columns = array(
            'price_id'      => $this->getIdFieldName(),
            'store_id'    => 'store_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price'         => 'value',
        );

        $columns = $this->_loadPriceDataColumns($columns);

        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId);

        $this->_loadPriceDataSelect($select);

        if (!is_null($storeId)) {
            $select->where('store_id = ?', array($storeId));
        }

        if (!is_null($customerGroup)) {
            $select->where('customer_group_id = ?', array($customerGroup));
        }

        return $adapter->fetchAll($select);
    }
}
