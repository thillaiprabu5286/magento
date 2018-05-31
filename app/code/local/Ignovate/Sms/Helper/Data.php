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


        $this->_url = 'https://2factor.in/API/R1/';
        $this->_apiKey = 'd01b7fb5-4941-11e8-a895-0200cd936042';
        $this->_senderId = 'Veggie';

        return $this;
    }

    public function sendSms($order, $template)
    {
        Mage::log("---------", null , 'sms.log');
        Mage::log($order->getIncrementId(), null, 'sms.log');
        $mobile = $order->getBillingAddress()->getTelephone();

        $data = array (
            'module'    => 'TRANS_SMS',
            'apikey'    => $this->_apiKey,
            'to'    => $mobile,
            'from'  => $this->_senderId,
            'templatename' => $template,
            'var1'  => $order->getIncrementId()
        );

        $url = $this->_url . "?" . http_build_query($data);
        Mage::log($url, null, 'sms.log');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        Mage::log($data, null, 'sms.log');

        return $data;
    }

    public function sendWelomeSms($mobile, $template)
    {

        $data = array (
            'module'    => 'TRANS_SMS',
            'apikey'    => $this->_apiKey,
            'to'    => $mobile,
            'from'  => $this->_senderId,
            'templatename' => $template,
            'var1'  => ' '
        );

        $url = $this->_url . "?" . http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

}