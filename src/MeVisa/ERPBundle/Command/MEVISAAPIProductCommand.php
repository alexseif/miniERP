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

class MEVISAAPIProductCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('mevisaapi:product:get')
        ->setDescription('Fetch products from mevisa');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    try {
      $username = "crmscript";
      $password = "jdjnde7352jdd";
      $url = "http://mevisa.ru/api/v1/products";
      $curl = new \Curl\Curl();
      $curl->setBasicAuthentication($username, $password);
      $curl->get($url);
      $response = json_decode($curl->response);
      $curl->close();
    } catch (\Exception $exc) {
//      dump($exc);
//TODO: do something
    }

    $em = $this->getContainer()->get('doctrine')->getManager();
    $data = $response->data;
    if ($data) {
      foreach ($data as $product) {
        $this->newProduct($product->product_ref, $product->title, $product->country, 0);
      }
    }
  }

  public function newProduct($ref, $name, $country, $unitPrice)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();
    $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $ref));
    if (!$product) {
      $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('name' => $name));
      if (!$product) {
        $product = new Products();
        $product->setEnabled(true);
        $product->setName($name);
        $product->setCountry($country);
        $product->setWcId($ref);
        $product->setRequiredDocuments(array());
        $productPrice = new ProductPrices();
        $productPrice->setCost(0);
        $productPrice->setPrice($unitPrice * 100);
        $product->addPricing($productPrice);
        $em->persist($product);
        $em->flush($product);
      }
    } else {
      $product->setName($name);
      $product->setCountry($country);
      $em->persist($product);
      $em->flush($product);
    }
    return $product;
  }

}
