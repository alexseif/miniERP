<?php

namespace MeVisa\ERPBundle\API;

use WC_API_Client;
use WC_API_Client_Exception;

/**
 * Description of WooCommerceWrapper
 *
 * @author alexseif
 */
class WooCommerceWrapper
{

    protected $customerKey = 'ck_9d919c4a36ac66767710ebaa70e3c3c6f8983443';
    protected $customerSecret = 'cs_637a98b84410fc2afbbdac580e438a21a5d20ad3';
    protected $storeUrl = 'http://themes.alexseif.com/Alpine-Theme/';

    public function getOrders()
    {
        $options = array(
            'ssl_verify' => false,
        );
        try {

            $client = new WC_API_Client($this->storeUrl, $this->customerKey, $this->customerSecret, $options);
        } catch (WC_API_Client_Exception $e) {

            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;

            if ($e instanceof WC_API_Client_HTTP_Exception) {

                print_r($e->get_request());
                print_r($e->get_response());
            }
        }
        return $client->orders->get();
    }

}
