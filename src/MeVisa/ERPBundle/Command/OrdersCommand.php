<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrdersCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('orders:fix')
        ->setDescription('Fix order dates');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();
    $wcos = $this->getContainer()->get('erp.wcorder');
    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findWC();
    foreach ($orders as $order) {
      $wcos->fixOrder($order);
      $em->persist($order);
    }
    $em->flush();
  }

}
