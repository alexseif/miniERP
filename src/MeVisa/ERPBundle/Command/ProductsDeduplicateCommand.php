<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsDeduplicateCommand extends ContainerAwareCommand
{

  private $em, $productRepo;

  protected function configure()
  {
    $this
        ->setName('products:dedupicate')
        ->setDescription('Fix order dates');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->em = $this->getContainer()->get('doctrine')->getManager();
    $this->productRepo = $this->em->getRepository('MeVisaERPBundle:Products');
    $products = $this->getAllProducts();

    foreach ($products as $product) {
      $this->fixOrders($product);
    }
  }

  public function fixOrders($product)
  {
    $sameNameProducts = $this->getProductsByName($product);

    $selectedProduct = reset($sameNameProducts);
    foreach ($sameNameProducts as $sameNameProduct) {
      foreach ($sameNameProduct->getOrderProducts() as $orderProducts) {
        $orderProducts->setProduct($selectedProduct);
      }
      foreach ($sameNameProduct->getPricing() as $pricing) {
        $this->em->remove($pricing);
      }
      if ($sameNameProduct != $selectedProduct) {
        $this->em->remove($sameNameProduct);
      }
    }
    $this->em->flush();
  }

  public function getAllProducts()
  {
    return $this->productRepo->findAll();
  }

  public function getProductsByName($product)
  {
    return $this->productRepo->findBy(array('name' => $product->getName()));
  }

}
