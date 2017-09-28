<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use MeVisa\CRMBundle\Service\CustomerAPIService;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Entity\ProductPrices;

class OrderAPIService
{

  protected $em;
  protected $CustomerAPIService;
  protected $tz;
  public $Order;

  /**
   * 
   * @param EntityManager $em
   * @param CustomerAPIService $CustomerAPIService
   */
  public function __construct(EntityManager $em, \MeVisa\CRMBundle\Service\CustomerAPIService $CustomerAPIService)
  {
    $this->em = $em;
    $this->repo = $this->em->getRepository('MeVisaERPBundle:Orders');
    $this->CustomerAPIService = $CustomerAPIService;
    $this->tz = new \DateTimeZone('UTC');
  }

  /**
   * 
   * @param string $channel
   * @param string $APIOrderID
   * @return Orders|null The Orders entity instance or NULL if the entity can not be found.
   */
  public function checkOrderExists($channel, $APIOrderID)
  {
    return $this->repo->findOneBy(array(
          'channel' => $channel,
          'wcId' => $APIOrderID
    ));
  }

  public function newOrder()
  {
    $this->Order = new Orders();
  }

  /**
   * 
   * @param string $name
   * @param string $email
   * @param string $phone
   * @param boolean $isAgent
   */
  public function newCustomer($name, $email, $phone, $isAgent = false)
  {
    $customer = $this->CustomerAPIService->newCustomer($name, $email, $phone, $isAgent);
    $this->Order->setCustomer($customer);
  }

  /**
   * 
   * @param string $ref
   * @param string $name
   * @param int $unitPrice
   * @return \MeVisa\ERPBundle\Service\Products
   */
  public function newProduct($ref, $name, $unitPrice)
  {
    $product = new Products();
    $product->setEnabled(true);
    $product->setName($name);
    $product->setWcId($ref);
    $product->setRequiredDocuments(array());
    $productPrice = new ProductPrices();
    $productPrice->setCost(0);
    $productPrice->setPrice($unitPrice);
    $product->addPricing($productPrice);
    $this->em->persist($product);
    return $product;
  }

  /**
   * 
   * @param string $ref
   * @param string $name
   * @param int $unitPrice
   * @param int $qty
   * @param int $total
   */
  public function setProduct($ref, $name, $unitPrice, $qty, $total)
  {
    $product = $this->em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $ref));
    if (!$product) {
      $product = $this->newProduct($ref, $name, $unitPrice);
    }

    //TODO: 1) Check product cost structure
    //TODO: 2) Recalculate cost

    $productPrice = $product->getPricing()->last();
    $orderProduct = new OrderProducts();
    $orderProduct->setProduct($product);
    $orderProduct->setQuantity($qty);
    $orderProduct->setUnitPrice($unitPrice);
    $orderProduct->setUnitCost($productPrice->getCost());
//    $orderProduct->setVendor($product->getVendor());
    $orderProduct->setTotal($total);

    $this->Order->addOrderProduct($orderProduct);
  }

  public function persist()
  {
    $this->em->persist($this->Order);
  }

  public function saveAllOrders()
  {
    $this->em->flush();
  }

}
