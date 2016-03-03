<?php

namespace MeVisa\ERPBundle\WCAPI;

//use Automattic\WooCommerce\Client;

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
            'version' => 'v2',
            'debug' => true,
            'return_as_array' => true
        );
        $this->client = new \WC_API_Client($this->storeUrl, $this->customerKey, $this->customerSecret, $this->options);
    }

    public function getIndex()
    {
        return $this->client->index->get();
    }

    public function getOrdersCount()
    {
        return $this->client->orders->get_count();
    }

    public function getOrders()
    {
        return $this->client->orders->get();
    }

    public function getCompletedOrders()
    {
        $parameters = array(
//            "status" => "completed",
        );
        $orders = $this->client->orders->get(null, $parameters);
        return $orders;
    }
    public function getCompletedOrdersSecondPage()
    {
        $parameters = array(
//            "status" => "completed",
            "page" => "2",
        );
        $orders = $this->client->orders->get(null, $parameters);
        return $orders;
    }

    public function getOrdersStatuses()
    {
        return $this->client->orders->get_statuses();
    }

    public function getOrder($orderId)
    {
        return $this->client->orders->get($orderId);
    }

    public function getOrderNotes($orderId)
    {
        return $this->client->order_notes->get($orderId);
    }

    public function getWebhooks()
    {
        return $this->client->webhooks->get();
    }

    public function getWebhook($webhookId)
    {
        return $this->client->webhooks->get($webhookId);
    }

    public function createWebhook()
    {
        $data = array(
            'name' => 'ERP Webhook',
            'secret' => 'kfxLneHxN7',
            'topic' => 'order.created',
            'delivery_url' => 'http://visallc.nichost.ru/admin/wc/new'
        );
        $result = $this->client->webhooks->create($data);
        return $result;
    }

    public function activateWebhook($webhookId)
    {
        //FIXME: not working
        $data = array('status' => "activate");
        $result = $this->client->webhooks->update($webhookId, $data);
        return array(
            'result' => $result,
        );
    }

}
