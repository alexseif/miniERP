<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\API\WooCommerceWrapper;

class APIController extends Controller
{

    /**
     * @Route("/getOrders")
     * @Template()
     */
    public function getOrdersAction()
    {
        $wooCommerce = new WooCommerceWrapper();
        return array(
            'orders' => $wooCommerce->getOrders()
        );
    }

    /**
     * @Route("/webhook")
     * @Template()
     */
    public function getWebhookAction()
    {
        $wooCommerce = new WooCommerceWrapper();
        return array(
            'orders' => $wooCommerce->getOrders()
        );
    }

}
