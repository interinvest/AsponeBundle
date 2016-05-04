<?php

namespace InterInvest\AsponeBundle\Command;

use InterInvest\AsponeBundle\Test\TestDeclarableTva;
use InterInvest\AsponeBundle\Test\TestDeposit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestDepositDetailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('aspone:test:depositdetail')
            ->setDescription('Test détail déposit')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('aspone.services.monitoring');
        $deposit = new TestDeposit();
        try {
            $response = $service->setDepositDetails($deposit);

            echo print_r($response, true) . "\n";

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
        }
    }
}