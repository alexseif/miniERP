<?php

namespace WooCommerceBundle\RESTAPI;

use Automattic\WooCommerce\Client;

/**
 * Description of RESTAPI
 *
 * @author alexseif
 */
class RESTAPI
{

    protected $storeUrl;
    protected $customerKey;
    protected $customerSecret;
    protected $options;
    protected $client;

    public function __construct()
    {

        $this->storeUrl = 'http://www.mevisa.ru/';
        $this->customerKey = 'ck_45ab000e9b9573a64470fbfca826d743';
        $this->customerSecret = 'cs_7ef7dd81ca0ad15699efc8089f369e8f';
        $this->options = array(
            'ssl_verify' => false,
            'version' => 'v2'
        );
        $this->client = new Client($this->storeUrl, $this->customerKey, $this->customerSecret, $this->options);
    }

    public function getIndex()
    {
        return $this->client->get('');
    }

    public function getOrdersCount()
    {
        return $this->client->get('orders/count');
    }
    public function getOrders()
    {
        return $this->client->get('orders');
    }

    public function getOrdersStatuses()
    {
        return $this->client->get('orders/statuses');
    }

    public function getOrder($orderId)
    {
        return $this->client->get('orders/' . $orderId);
    }

    public function getOrderNotes($orderId)
    {
        return $this->client->get('orders/' . $orderId . '/notes');
    }

    public function getWebhooks()
    {
        return $this->client->get('webhooks');
    }

}
