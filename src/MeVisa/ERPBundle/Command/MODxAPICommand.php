<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Entity\OrderCompanions;
use MeVisa\ERPBundle\Entity\OrderPayments;
use MeVisa\ERPBundle\Entity\OrderComments;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Entity\ProductPrices;
use MeVisa\CRMBundle\Entity\Customers;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class MODxAPICommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('modxapi:get')
        ->setDescription('Fetch orders from MODx');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $modxOrders = null;
    $container = $this->getContainer();

    $modxOrders = json_decode($this->fetchOrders());

    $em = $this->getContainer()->get('doctrine')->getManager();
    if ($modxOrders) {
      foreach ($modxOrders as $modxOrder) {
        $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('channel' => 'uaevisa.ru', 'wcId' => $modxOrder->id));
        if (!$order) {
          $order = $this->newOrder($em, $modxOrder);
        }
        $em->persist($order);
      }
    }
    $em->flush();
  }

  public function fetchOrders()
  {
    $today = new \DateTime();
    $today->setTime(00, 00, 00);
    try {
      $curl = new \Curl\Curl();
      $curl->get('https://uaevisa.ru/api/v1/orders', array(
        'login' => 'api',
        'pass' => 'YrrLeqhb',
        'startdate' => $today->format('Y-m-d H:i'),
        'limit' => '100'
      ));
      $response = $curl->response;
      $curl->close();
    } catch (\Exception $exc) {
//      dump($exc);
//TODO: do something
    }
    return $response;
  }

  public function newOrder($em, $modxOrder)
  {
    $timezone = new \DateTimeZone('UTC');

    $order = new Orders();

    $customer = new Customers();
    $customer->setName($modxOrder->customerDetails->name);
    $customer->setEmail($modxOrder->customerDetails->email);
    $customer->setPhone($modxOrder->customerDetails->phone);

    $customerExists = $em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array('email' => $customer->getEmail()));

    if (!$customerExists) {
      $em->persist($customer);
      $order->setCustomer($customer);
    } else {
      $order->setCustomer($customerExists);
      $orderCommentText = '';
      // Emails do not match
      if ($customer->getName() != $customerExists->getName()) {
        //TODO: do something
        //$customerExists->setName($customer->getName());
      }
      if ($customer->getEmail() != $customerExists->getEmail()) {
        //TODO: do something
      }
      // Phones do not match
      if ($customer->getPhone() != $customerExists->getPhone()) {
        //TODO: do something
        $customerExists->setPhone($customer->getPhone());
      }
      if ('' != $orderCommentText) {
        $orderComment = new OrderComments();
        $orderComment->setComment($orderCommentText);
        $order->addOrderComment($orderComment);
      }
    }

    $this->setOrderDetails($modxOrder, $order, $timezone, $em);
    return $order;
  }

  protected function setOrderDetails($modxOrder, $order, $timezone, $em)
  {
    $order->setWcId($modxOrder->id);
    $order->setNumber('UV' . $modxOrder->id);
    $order->setCreatedAt(new \DateTime($modxOrder->orderDate, $timezone));
    $order->setState("backoffice");

    $order->setPeople($modxOrder->pax);
    $order->setTotal($modxOrder->orderTotal * 100);
    $order->setProductsTotal($modxOrder->orderTotal * 100);
    $order->setAdjustmentTotal(0);

    $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $modxOrder->orderDetails->productRef));
    if (!$product) {
      $product = new Products();
      $product->setEnabled(true);
      $product->setWcId($modxOrder->orderDetails->productRef);
      $product->setName($modxOrder->orderDetails->productName);
      $product->setRequiredDocuments(array());
      $product->setUrgent(false);
      $productPrice = new ProductPrices();
      $productPrice->setCost(0);
      $productPrice->setPrice($modxOrder->orderDetails->productUnitPrice * 100);
      $product->addPricing($productPrice);
      $em->persist($product);
    }


    $productPrice = $product->getPricing()->last();
    $orderProduct = new OrderProducts();
    $orderProduct->setProduct($product);
    $orderProduct->setQuantity($modxOrder->orderDetails->productQuantity);
    $orderProduct->setUnitPrice($modxOrder->orderDetails->productUnitPrice * 100);
    $orderProduct->setUnitCost($productPrice->getCost());
    $orderProduct->setVendor($product->getVendor());
    $orderProduct->setTotal($modxOrder->orderDetails->productUnitPrice * $modxOrder->orderDetails->productQuantity * 100);

    $order->addOrderProduct($orderProduct);

    $order->setPeople($modxOrder->pax);
    $arrival = \DateTime::createFromFormat('Y-m-d', $modxOrder->arrivalDate, $timezone);
    if ($arrival) {
      $order->setArrival($arrival);
    } else {
      //TODO: Report issue
      dump('Invalid arrival for Order: ' . $order->getNumber());
    }
    $departure = \DateTime::createFromFormat("Y-m-d", $modxOrder->departureDate, $timezone);
    if ($departure) {
      $order->setDeparture($departure);
    } else {
      //TODO: Report issue
      dump('Invalid departure for Order: ' . $order->getNumber());
    }

    $orderPayment = new OrderPayments();
    // method_id method_title
    $orderPayment->setMethod('payu');
    $orderPayment->setAmount($modxOrder->orderTotal * 100);
    $orderPayment->setCreatedAt(new \DateTime($modxOrder->orderDate, $timezone));
    $orderPayment->setState("paid");
    $order->addOrderPayment($orderPayment);

    if ("" != $modxOrder->comment) {
      $orderComment = new OrderComments();
      $orderComment->setComment($modxOrder->comment . "-- Customer: " . $order->getCustomer()->getName());
      $orderComment->setCreatedAt(new \DateTime($modxOrder->orderDate, $timezone));
      $order->addOrderComment($orderComment);
    }

    foreach ($modxOrder->orderCompanions as $companion) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($companion->name);
      $order->addOrderCompanion($orderCompanion);

      $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
      $document->setName($companion->file);
      // http://uaevisa.ru/assets/uploads/74/0/contact_pasp_2016-09-06_12-54-15.png
      $document->setPath($companion->file);
      $order->addOrderDocument($document);
    }


    //FIXME: Dynamic channel
    $order->setChannel("uaevisa.ru");


    $order->setTicketRequired(false);
  }

}
