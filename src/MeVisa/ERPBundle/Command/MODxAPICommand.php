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

    $mysqlLink = mysqli_connect($container->getParameter('uaevc_host'), $container->getParameter('uaevc_user'), $container->getParameter('uaevc_password'), $container->getParameter('uaevc_name'));

    mysqli_query($mysqlLink, "SET NAMES UTF8");
    $query = 'SELECT * FROM `modx_prazdnik_items` WHERE createdon >= NOW() - INTERVAL 1 DAY';
    $mysqlResultLink = mysqli_query($mysqlLink, $query);
    while ($row = mysqli_fetch_assoc($mysqlResultLink)) {
      $modxOrders[] = $row;
    }

    $em = $this->getContainer()->get('doctrine')->getManager();
    if ($modxOrders) {
      foreach ($modxOrders as $modxOrder) {
        if (1 == $modxOrder['active']) {
          $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('channel' => 'uaevisa.ru', 'wcId' => $modxOrder['id']));
          if (!$order) {
            $order = $this->newOrder($em, $modxOrder);
          }
          $em->persist($order);
        }
      }
    }
    $em->flush();
  }

  public function newOrder($em, $modxOrder)
  {
    $timezone = new \DateTimeZone('UTC');

    $order = new Orders();

    $customer = new Customers();
    $customer->setName($modxOrder['name'] . ' ' . $modxOrder['famil']);
    $customer->setEmail($modxOrder['email']);
    $customer->setPhone($modxOrder['phone']);

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
    $order->setWcId($modxOrder['id']);
    $order->setNumber('UV' . $modxOrder['id']);
    $order->setCreatedAt(new \DateTime($modxOrder['createdon'], $timezone));
    $order->setState("backoffice");

    $order->setPeople($modxOrder['kolchel']);
    $order->setTotal($modxOrder['cost'] * 100);
    $order->setProductsTotal($modxOrder['cost'] * 100);
    $order->setAdjustmentTotal(0);

    $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('name' => $modxOrder['namezakaz']));
    if (!$product) {
      $product = new Products();
      $product->setEnabled(true);
      $product->setName($modxOrder['namezakaz']);
      $product->setRequiredDocuments(array());
      $product->setUrgent(false);
      $productPrice = new ProductPrices();
      $productPrice->setCost(0);
      $productPrice->setPrice(($modxOrder['cost'] * 100) / $modxOrder['kolchel']);
      $product->addPricing($productPrice);
      $em->persist($product);
    }


    $productPrice = $product->getPricing()->last();
    $orderProduct = new OrderProducts();
    $orderProduct->setProduct($product);
    $orderProduct->setQuantity($modxOrder['kolchel']);
    $orderProduct->setUnitPrice(($modxOrder['cost'] * 100) / $modxOrder['kolchel']);
    $orderProduct->setUnitCost($productPrice->getCost());
    $orderProduct->setVendor($product->getVendor());
    $orderProduct->setTotal($modxOrder['cost'] * 100);

    $order->addOrderProduct($orderProduct);

    $order->setPeople($modxOrder['kolchel']);
    $arrival = \DateTime::createFromFormat('Y-m-d G:i:s', $modxOrder['datet'], $timezone);
    if ($arrival) {
      $order->setArrival($arrival);
    } else {
      //TODO: Report issue
      dump('Invalid arrival for Order: ' . $order->getNumber());
    }
    $departure = \DateTime::createFromFormat("Y-m-d G:i:s", $modxOrder['dateo'], $timezone);
    if ($departure) {
      $order->setDeparture($departure);
    } else {
      //TODO: Report issue
      dump('Invalid departure for Order: ' . $order->getNumber());
    }

    $accessBasePath = 'http://uaevisa.ru/assets/uploads/';
    $uploadBasePath = '/srv/www/uaevisa/assets/uploads/';
    $fs = new Filesystem();
    if ($fs->exists($uploadBasePath . $order->getWcId())) {
      $finder = new Finder();

      $finder->files()->in($uploadBasePath . $order->getWcId());
      foreach ($finder as $file) {
        $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
        $document->setName($file->getFileName());
        // http://uaevisa.ru/assets/uploads/74/0/contact_pasp_2016-09-06_12-54-15.png
        $document->setPath($accessBasePath . $order->getWcId() . '/' . $file->getRelativePathname());
        $order->addOrderDocument($document);
      }
    }

    $orderPayment = new OrderPayments();
    // method_id method_title
    $orderPayment->setMethod('payu');
    $orderPayment->setAmount($modxOrder['cost'] * 100);
    $orderPayment->setCreatedAt(new \DateTime($modxOrder['editedon'], $timezone));
    if (1 == $modxOrder['active']) {
      $orderPayment->setState("paid");
    } else {
      $orderPayment->setState("not_paid");
    }
    $order->addOrderPayment($orderPayment);

    if ("" != $modxOrder['description']) {
      $orderComment = new OrderComments();
      $orderComment->setComment($modxOrder['note'] . "-- Customer: " . $order->getCustomer()->getName());
      $orderComment->setCreatedAt(new \DateTime($modxOrder['created_at'], $timezone));
      $order->addOrderComment($orderComment);
    }

    if ("" != $modxOrder["name1"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name1"] . $modxOrder["famil1"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name2"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name2"] . $modxOrder["famil2"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name3"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name3"] . $modxOrder["famil3"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name4"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name4"] . $modxOrder["famil4"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name5"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name5"] . $modxOrder["famil5"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name6"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name6"] . $modxOrder["famil6"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name7"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name7"] . $modxOrder["famil7"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name8"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name8"] . $modxOrder["famil8"]);
      $order->addOrderCompanion($orderCompanion);
    }
    if ("" != $modxOrder["name9"]) {
      $orderCompanion = new OrderCompanions();
      $orderCompanion->setName($modxOrder["name9"] . $modxOrder["famil9"]);
      $order->addOrderCompanion($orderCompanion);
    }

    //FIXME: Dynamic channel
    $order->setChannel("uaevisa.ru");


    $order->setTicketRequired(false);
  }

}
