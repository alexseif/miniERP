<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use \MeVisa\ERPBundle\Entity\Orders;
use Gedmo\Loggable\LoggableListener;

/**
 * This class deals with the quantity & price problems 
 * sent by WooCommerce from MeVisa.ru
 *
 * @author alexseif
 */
class WCOrderService
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function fixOrder($order)
    {


        $orderProducts = $order->getOrderProducts();
        foreach ($orderProducts as $orderProduct) {
            $qtyValid = true;
            if ($orderProduct->hasMessages()) {
                continue;
            }

            // Get Price at time of Order
            $productPrice = $this->em->getRepository('MeVisaERPBundle:ProductPrices')
                    ->findProductPriceAtDate($orderProduct->getProduct()->getId(), $order->getCreatedAt());
            if (!$productPrice) {
                throw new \Exception('Could not find a price for Order:' . $orderProduct->getOrderRef()->getId() . ' Product ' . $orderProduct->getProduct()->getName() . ' ID:' . $orderProduct->getProduct()->getId());
            }

            //Check Quantity
            $estQuantity = $orderProduct->getTotal() / $productPrice->getPrice();

            if ($orderProduct->getQuantity() != $estQuantity) {
                // Quantity needs updated 
                if ($this->isQuantityValid($estQuantity)) {
                    $orderProduct->newMessage("NOTICE", "Quanitity updated from " . $orderProduct->getQuantity() . " to " . $estQuantity);
                    $orderProduct->setQuantity($estQuantity);
                } else {
                    $qtyValid = false;
                    $orderProduct->newMessage("ERROR", "Quanitity requires update, require manual intervention");
                    $orderProduct->newMessage("ERROR", "Price requires update price should be " . $productPrice->getPrice() / 100);
                    $orderProduct->newMessage("ERROR", "Cost requires updated, cost should be " . $productPrice->getCost() / 100);
                }
            }

            // Check Price
            if (($orderProduct->getUnitPrice() != $productPrice->getPrice()) && $qtyValid) {
                $orderProduct->setUnitPrice($productPrice->getPrice());
                $orderProduct->newMessage("NOTICE", "Price updated from " . $orderProduct->getUnitPrice() / 100 . " to " . $productPrice->getPrice() / 100);
            }

            // Check Cost
            if (($orderProduct->getUnitCost() != $productPrice->getCost()) && $qtyValid) {
                $orderProduct->newMessage("NOTICE", "Cost updated from " . $orderProduct->getUnitCost() / 100 . " to " . $productPrice->getCost() / 100);
                $orderProduct->setUnitCost($productPrice->getCost());
            }

            return $order;
        }
    }

    public function isQuantityValid($qty)
    {
        if (is_int($qty) > 1) {
            return true;
        } else {
            return false;
        }
    }

//TODO: move to Order
    public function calulcateOrderTotal()
    {
        
    }

//TODO: move to Order
    public function checkOrderPayemnt()
    {
        
    }

}
