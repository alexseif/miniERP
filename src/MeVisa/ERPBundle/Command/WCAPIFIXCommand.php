<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WCAPIFIXCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('wcapi:fix')
                ->setDescription('Fetch orders from WC');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $apiOrders = $em->getRepository('MeVisaERPBundle:Orders')->findWC(array('wcId' => 'NOT NULL'));
        foreach ($apiOrders as $order) {
            $arrival = $order->getArrival();
            $order->setArrival($order->getDeparture());
            $order->setDeparture($arrival);
        }
        $em->flush();
    }

}
