<?php

namespace InterInvest\AsponeBundle\Command;

use InterInvest\AsponeBundle\Test\TestDeclarableTva;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestDepositCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('aspone:test:deposit')
            ->setDescription('Test envoi déposit')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Chemin du fichier à transmettre')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type de dépot')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('aspone.services.deposit');

        try {
            $xmlDocument = simplexml_load_file($input->getOption('path'));
            $response = $service->sendDeposit($xmlDocument, $input->getOption('type'));

            echo print_r($response, true) . "\n";
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
        }
    }
}