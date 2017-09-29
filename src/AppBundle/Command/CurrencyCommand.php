<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('api:currency:get')
        ->setDescription('Fetch latest RUB price from Open Exchance Rates');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $openExchangeRatesService = $this->getContainer()->get('open_exchange_rates_service');
    $rates = $openExchangeRatesService->getLatest(['RUB']);

    $em = $this->getContainer()->get('doctrine')->getManager();

    foreach ($rates['rates'] as $currencyCode => $rate) {
      $currency = new \AppBundle\Entity\Currency();
      $currency->setCur1($rates['base']);
      $currency->setCur2($currencyCode);
      $currency->setValue($rate * 100);
      $currency->setCreatedAt(new \DateTime("@" . $rates['timestamp']));

      $em->persist($currency);
    }

    $em->flush();
  }

}
