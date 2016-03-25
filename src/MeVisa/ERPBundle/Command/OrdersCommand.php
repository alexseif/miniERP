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

        $ordersRepo = $em->getRepository('MeVisaERPBundle:Orders');

        $findBy = array(
            'state' => 'rejected',
            'completedAt' => NULL
        );
        $orders = $ordersRepo->findBy($findBy);
        $this->fixDates($orders, $output);
        
        $findBy = array(
            'state' => 'rejected',
            'postedAt' => NULL
        );
        $orders = $ordersRepo->findBy($findBy);
        $this->fixDates($orders, $output);
        
        $findBy = array(
            'state' => 'approved',
            'completedAt' => NULL
        );
        $orders = $ordersRepo->findBy($findBy);
        $this->fixDates($orders, $output);
        
        $findBy = array(
            'state' => 'approved',
            'postedAt' => NULL
        );
        $orders = $ordersRepo->findBy($findBy);
        $this->fixDates($orders, $output);

        $em->flush();
        $output->writeln('complete');
    }

    protected function fixDates($orders, $output)
    {
        foreach ($orders as $order) {
            $output->writeln('Order: ' . $order->getId() . ' State: ' . $order->getState());
            $orderLog = $this->getContainer()->get('erp.order')->getOrderLog($order->getId());
            foreach ($orderLog as $log) {
                $data = $log->getData();
                if (key_exists('state', $data)) {
                    if ('post' == $data['state']) {
                        if (empty($order->getPostedAt())) {
                            $order->setPostedAt($log->getLoggedAt());
                            $output->writeln('  Updated postedAt to: ' . $order->getPostedAt()->format('Y-m-d H:i:s'));
                        } else {
                            $output->writeln('  Neglecting: ' . $log->getLoggedAt()->format('Y-m-d H:i:s'));
                        }
                    } elseif ('approved' == $data['state'] || 'rejected' == $data['state']) {
                        if (empty($order->getCompletedAt())) {
                            $order->setCompletedAt($log->getLoggedAt());
                            $output->writeln('  Updated completedAt to: ' . $order->getCompletedAt()->format('Y-m-d H:i:s'));
                        } else {
                            $output->writeln('  Neglecting: ' . $log->getLoggedAt()->format('Y-m-d H:i:s'));
                        }
                    }
                }
            }
        }
        $output->writeln('Found: ' . count($orders));
    }

}
