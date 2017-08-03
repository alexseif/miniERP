<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MeVisa\ERPBundle\WCAPI\RESTAPI;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Entity\OrderPayments;
use MeVisa\ERPBundle\Entity\OrderComments;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Entity\ProductPrices;
use MeVisa\CRMBundle\Entity\Customers;
use WC_API_Client_HTTP_Exception;

class MEVISAAPICommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('mevisaapi:get')
        ->setDescription('Fetch orders from mevisa');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    try {
      $username = "crmscript";
      $password = "jdjnde7352jdd";
      $url = "http://mevisa.ru/api/v1/orders";
      $curl = new \Curl\Curl();
      $curl->setBasicAuthentication($username, $password);
      $curl->get($url);
      $response = json_decode($curl->response);
      $curl->close();
    } catch (\Exception $exc) {
//      dump($exc);
      //TODO: do something
    }
//    dump($response->data);
//    die();
    $em = $this->getContainer()->get('doctrine')->getManager();
    $data = $response->data;
    if ($data) {
      foreach ($data as $mevisaOrder) {
//        if ("paid" == $mevisaOrder['payment_state']) {
        $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('channel' => 'MeVisa.ru', 'wcId' => "mv" . $mevisaOrder->id_order));
        if (!$order) {
          $order = $this->newOrder($em, $mevisaOrder);
        }
        $em->persist($order);
//        }
      }
    }
    $em->flush();
  }

  public function newOrder($em, $mevisaOrder)
  {
    $timezone = new \DateTimeZone('UTC');

    $order = new Orders();

    $customer = new Customers();
    $customer->setName($mevisaOrder->customer_name);
    $customer->setEmail($mevisaOrder->customer_email);
    $customer->setPhone($mevisaOrder->customer_phone);

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
    $this->setOrderDetails($mevisaOrder, $order, $timezone, $em);
    return $order;
  }

  protected function setOrderDetails($mevisaOrder, $order, $timezone, $em)
  {
    $order->setWcId("mv".$mevisaOrder->id_order);
    $order->setNumber("mv".$mevisaOrder->id_order);
    $order->setCreatedAt(new \DateTime($mevisaOrder->order_date, $timezone));
    switch ($mevisaOrder->order_state) {
      case "cancelled":
      case "failed":
        $state = "cancelled";
        break;
      case "refunded":
        $state = "refunded";
        break;
      case "pending":
      case "processing":
      case "on-hold":
      case "completed":
      default:
        $state = "backoffice";
        break;
    }

    $order->setState($state);
    $order->setTotal($mevisaOrder->order_total * 100);
    $order->setProductsTotal($mevisaOrder->order_total * 100);
    $order->setPeople($mevisaOrder->pax);
    $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $mevisaOrder->product_ref));
    if (!$product) {
      $product = new Products();
      $product->setEnabled(true);
      $product->setName($mevisaOrder->product_name);
      $product->setWcId($mevisaOrder->product_ref);
      $product->setRequiredDocuments(array());
      $productPrice = new ProductPrices();
      $productPrice->setCost(0);
      $productPrice->setPrice($mevisaOrder->product_unitprice * 100);
      $product->addPricing($productPrice);
      $em->persist($product);
    }

    //TODO: 1) Check product cost structure
    //TODO: 2) Recalculate cost

    $productPrice = $product->getPricing()->last();
    $orderProduct = new OrderProducts();
    $orderProduct->setProduct($product);
    $orderProduct->setQuantity($mevisaOrder->product_quantiry);
    $orderProduct->setUnitPrice($mevisaOrder->product_unitprice * 100);
    $orderProduct->setUnitCost($productPrice->getCost());
    $orderProduct->setVendor($product->getVendor());
    $orderProduct->setTotal($mevisaOrder->order_total * 100);

    $order->addOrderProduct($orderProduct);
    if ($mevisaOrder->arrival_date) {
      $arrival = \DateTime::createFromFormat("d/m/Y", $mevisaOrder->arrival_date, $timezone);
      $order->setArrival($arrival);
    }
    if ($mevisaOrder->departure_date) {
      $departure = \DateTime::createFromFormat("d/m/Y", $mevisaOrder->departure_date, $timezone);
      $order->setDeparture($departure);
    }
    foreach ($mevisaOrder->files as $file) {
      $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
      $document->setName($file->title);
      // http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/3975-915-img_9996.jpg
      $document->setPath($file->path);
      $order->addOrderDocument($document);
    }
    $order->setAdjustmentTotal(0);
    if ("paid" == $mevisaOrder->payment_state) {
      $orderPayment = new OrderPayments();
      // method_id method_title
      $orderPayment->setMethod("CC");
      $orderPayment->setAmount($mevisaOrder->order_total * 100);
      $orderPayment->setCreatedAt(new \DateTime($mevisaOrder->order_date, $timezone));
      $orderPayment->setState("paid");
      $order->addOrderPayment($orderPayment);
    }
    //FIXME: Dynamic channel
    $order->setChannel("MeVisa.ru");


    $order->setTicketRequired(false);
  }

}
