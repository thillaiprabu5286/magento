<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 05/10/16
 * Time: 3:17 PM
 */
class Ignovate_Sms_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_url;
    protected $_apiKey;
    protected $_senderId;

    public function __construct()
    {
        /*if (Mage::getStoreConfig('ignovateconfig/smsgateway/enabled') == 1) {
            $this->_url = Mage::getStoreConfig('ignovateconfig/smsgateway/api_url');
            $this->_apiKey = Mage::getStoreConfig('ignovateconfig/smsgateway/api_key');
            $this->_senderId = Mage::getStoreConfig('ignovateconfig/smsgateway/sender_id');
        }*/


        $this->_url = 'https://2factor.in/API/R1/module';
        $this->_apiKey = 'd01b7fb5-4941-11e8-a895-0200cd936042&to=9840297768';
        $this->_senderId = 'Veggie';

        return $this;
    }

    public function sendSms($order, $template)
    {
        $mobile = $order->getBillingAddress()->getTelephone();

        $data = array (
            'module'    => 'TRANS_SMS',
            'apiKey'    => $this->_apiKey,
            'from'  => $this->_senderId,
            'to'    => $mobile,
            'templatename' => $template,
            'var1'  => $order->getIncrementId()
        );

        $url = $this->_url . "?" . http_build_query($data);

        $client = curl_init($url);
        curl_setopt($client, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($client, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($client, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($client, CURLOPT_TIMEOUT,10);
        $output = curl_exec($client);
        $httpcode = curl_getinfo($client, CURLINFO_HTTP_CODE);
        curl_close($client);

        return $httpcode;
    }

}