<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LogCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('log:clean')
                ->setDescription('Clean log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $em = $this->getContainer()->get('doctrine')->getManager();
        $query = $em->createQuery('DELETE
                Gedmo\Loggable\Entity\LogEntry le
            WHERE le.action != :action
            AND le.username IS NULL
            ')
                ->setParameter("action", "create");
        $query->execute();
        $output->writeln('complete');
    }

}
