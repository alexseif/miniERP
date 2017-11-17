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

class MEVISAAPICommand extends ContainerAwareCommand
{

  protected $definitions;

  protected function configure()
  {
    $this
        ->setName('mevisaapi:get')
        ->setDescription('Fetch orders from mevisa');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
//TOOD: encapsulate and move
    $username = "crmscript";
    $password = "jdjnde7352jdd";
    $url = "https://mevisa.ru/api/v1/orders";
    try {
      $curl = new \Curl\Curl();
      $curl->setBasicAuthentication($username, $password);
      $curl->get($url);
      $response = json_decode($curl->response);
      $curl->close();
    } catch (\Exception $exc) {
//      dump($exc);
//TODO: do something
    }

    $data = $response->data;

    if ($data) {
      $em = $this->getContainer()->get('doctrine')->getManager();
      $OrderAPIService = $this->getApplication()->getKernel()->getContainer()->get('erp.order.api');

      foreach ($data as $mevisaOrder) {
        if ($mevisaOrder->id_order) {

          $orderExists = $OrderAPIService->checkOrderExists('Mevisa.ru', "mv" . $mevisaOrder->id_order);

          if (!$orderExists) {
            $timezone = new \DateTimeZone('UTC');

            $OrderAPIService->newOrder();
            $OrderAPIService->newCustomer(
                $mevisaOrder->customer_name
                , $mevisaOrder->customer_email
                , $mevisaOrder->customer_phone
            );

            //FIXME: Dynamic channel
            $OrderAPIService->Order->setChannel("MeVisa.ru");
            $OrderAPIService->Order->setWcId("mv" . $mevisaOrder->id_order);
            $OrderAPIService->Order->setNumber("mv" . $mevisaOrder->id_order);
            $OrderAPIService->Order->setCreatedAt(new \DateTime($mevisaOrder->order_date, $timezone));
            $OrderAPIService->Order->setState("backoffice");
            $OrderAPIService->Order->setTotal($mevisaOrder->order_total * 100);
            $OrderAPIService->Order->setProductsTotal($mevisaOrder->order_total * 100);
            $OrderAPIService->Order->setPeople($mevisaOrder->pax);
            $OrderAPIService->Order->setAdjustmentTotal(0);

            $OrderAPIService->setProduct(
                $mevisaOrder->product_ref
                , $mevisaOrder->product_name
                , $mevisaOrder->product_unitprice * 100
                , $mevisaOrder->product_quantiry
                , $mevisaOrder->order_total * 100
            );

            if ($mevisaOrder->arrival_date) {
              $arrival = new \DateTime($mevisaOrder->arrival_date, $timezone);
              $OrderAPIService->Order->setArrival($arrival);
            }

            if ($mevisaOrder->departure_date) {
              $departure = new \DateTime($mevisaOrder->departure_date, $timezone);
              $OrderAPIService->Order->setDeparture($departure);
            }

            foreach ($mevisaOrder->files as $file) {
              $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
              $document->setName($file->title);
              // http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/3975-915-img_9996.jpg
              $document->setPath($file->path);
              $OrderAPIService->Order->addOrderDocument($document);
            }
            $orderPayment = new OrderPayments();

            if ("paid" == $mevisaOrder->payment_state) {
              // method_id method_title
              $orderPayment->setMethod("CC");
              $orderPayment->setAmount($mevisaOrder->order_total * 100);
              $orderPayment->setCreatedAt(new \DateTime($mevisaOrder->order_date, $timezone));
              $orderPayment->setState("paid");
            } else {
              $orderPayment->setMethod("CC");
              $orderPayment->setAmount($mevisaOrder->order_total * 100);
              $orderPayment->setCreatedAt(new \DateTime($mevisaOrder->order_date, $timezone));
              $orderPayment->setState("not_paid");
            }
            $OrderAPIService->Order->addOrderPayment($orderPayment);

            $OrderAPIService->Order->setTicketRequired(false);
            $OrderAPIService->persist();
          }
        }
      }
      $OrderAPIService->saveAllOrders();
    }
  }

  public function newProduct($ref, $name, $unitPrice)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();
    $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $ref));
    if (!$product) {
      $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('name' => $name));
      if (!$product) {
        $product = new Products();
        $product->setEnabled(true);
        $product->setName($name);
        $product->setWcId($ref);
        $product->setRequiredDocuments(array());
        $productPrice = new ProductPrices();
        $productPrice->setCost(0);
        $productPrice->setPrice($unitPrice * 100);
        $product->addPricing($productPrice);
        $em->persist($product);
        $em->flush($product);
      }
    }
    return $product;
  }

}
