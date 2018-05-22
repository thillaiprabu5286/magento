<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 08/11/17
 * Time: 12:41 PM
 */
class Ignovate_Mobile_Model_Api2_Cart_Abstract extends Ignovate_Api2_Model_Resource
{
    protected function _buildQuote($quote)
    {
        $quoteData = array();

        $quoteData = array_merge($quoteData, array(
            'quote_id'                      => $quote->getId(),
            'subtotal'                      => $quote->getShippingAddress()->getSubtotal(),
            'subtotal_with_discount'        => $quote->getShippingAddress()->getSubtotalWithDiscount(),
            'grand_total'                   => $quote->getShippingAddress()->getGrandTotal(),
            'currency_code'                 => $quote->getQuoteCurrencyCode(),
            'cod_fee'                       => $quote->getShippingAddress()->getCodFee(),
            'shipping_fee'                  => $quote->getShippingAddress()->getShippingAmount(),
            'discount_amount'               => $quote->getShippingAddress()->getDiscountAmount(),
            'tax_amount'                    => $quote->getShippingAddress()->getTaxAmount()
        ));

        $productIds = array();
        foreach ($quote->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        //Build product response with wishlist
        $collectionSelect = $this->getAdapter()->select()
            ->from(
                array('product' => 'catalog_product_flat_' . $quote->getStoreId()),
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

        $collectionSelect->where(
            'product.entity_id IN (?)', $productIds
        );
        
        $indexData = $this->getAdapter()->query($collectionSelect)->fetchAll();
        foreach ($indexData as $key => $data) {
            $id = $data['product_id'];
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($quote->getCustomerId(), true);
            $collection = Mage::getModel('wishlist/item')->getCollection()
                ->addFieldToFilter('store_id', $quote->getStoreId())
                ->addFieldToFilter('wishlist_id', $wishlist->getId())
                ->addFieldToFilter('product_id', $id);
            $item = $collection->getFirstItem();
            $isWishlist = 0;
            if ($item->getId()) {
                $isWishlist = 1;
            }
            $data['is_wishlist'] = $isWishlist;
            $quoteData['items'][] = $data;
        }

        // Add address info into quote data
        $quoteData['customer'] = array (
            'customer_id'   => $quote->getCustomerId(),
            'customer_email' => $quote->getCustomerEmail()
        );
        $customerAddress = array();
        $customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
        foreach ($customer->getAddresses() as $address) {
            $customerAddress[] = $address->toArray();
        }
        $quoteData['customer']['address'] = $customerAddress;
        $quoteData['shipping'] = $this->getShippingMethods();

        $quoteData['payment'] = $this->getPaymentMethods();

        return $quoteData;
    }

    public function getShippingMethods($isMultiSelect = false)
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = array();
        foreach($methods as $_code => $_method)
        {
            if(!$_title = Mage::getStoreConfig("carriers/$_code/title"))
                $_title = $_code;
            $options[] = array(
                'value' => $_code . '_' . $_code,
                'label' => $_title,
                'rate'  => Mage::getStoreConfig("carriers/$_code/price")
            );
        }
        if($isMultiSelect) {
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }
        return $options;
    }

    public function getPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode == 'paypal_billing_agreement') {
                continue;
            }
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        return $methods;
    }

    protected function _getFinalPrice($product)
    {
        //Look for special price
        if ($product->getSpecialPrice() > 0) {
            $price = $product->getSpecialPrice();
        } else {
            $price = $product->getPrice();
        }

        return $price;
    }
}