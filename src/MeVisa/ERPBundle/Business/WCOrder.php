<?php

namespace MeVisa\ERPBundle\Business;

/**
 * This class deals with the quantity & price problems 
 * sent by WooCommerce from MeVisa.ru
 *
 * @author alexseif
 */
class WCOrder
{

    public function fixOrder(\MeVisa\ERPBundle\Entity\Orders $order)
    {
        //Check if it's a WC order
        if ("MeVisa.ru" == $order->getChannel() && !is_null($order->getWcId()))
        //Do something nice
            ;
        else
        //Do something bad
            ;
    }

    public function estimateQuantity()
    {
        // Get Price at time of Order
//        $price = $op->getProduct()->getPricing()->last()->getPrice();
        // Estimate Quantity
//        $qty = $op->getTotal() / $price;
        // Return Quantity
    }

    public function isQuantityValid()
    {
        // is quantity null or 0 return false
        // is quantity integer return true
        // else return false
    }

    public function fixPrice()
    {
        // isPriceCorrect return 
        // else compare OrderProductPrice to multiple of ProductPrice 
        // true, correct price & return
        // false, flag OrderProduct
    }

    public function isPriceCorrect()
    {
        // Get Price at time of Order
        // compare OrderProductPrice to ProductPrice return true
    }

    public function calulcateOrderTotal()
    {
        
    }

    public function checkOrderPayemnt()
    {
        
    }

}
