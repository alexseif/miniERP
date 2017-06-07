<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Curl\Curl;

class ModXTestCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
        ->setName('modx:test')
        ->setDescription('Test Modx Integration');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $curl = new Curl();
    $curl->get('http://uaevisa.ru/api/v1/orders', array(
      'login' => 'api',
      'pass' => 'YrrLeqhb',
      'startdate' => '2016-09-18 10:00',
      'limit' => '10'
    ));
    $response = $curl->response;
    $curl->close();
    \Doctrine\Common\Util\Debug::dump($response);
  }

}
