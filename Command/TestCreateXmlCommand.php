<?php

namespace InterInvest\AsponeBundle\Command;

use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\AsponeBundle\Test\TestDeclarableTva;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCreateXmlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('aspone:test:createxml')
            ->setDescription('Test création xml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('aspone.services.xml');
        $path = $this->getContainer()->get('kernel')->getRootDir() . $this->getContainer()->getParameter('aspone.xmlPath');

        $declarable = new TestDeclarableTva();


        try {
            $xml = $service->setXmlFromDeclarable($declarable);

            $declaration = new Declaration();
            $declaration->setType($declarable->getType());
            $declaration->setEtat(Declaration::ETAT_NON_FINIE);

            //save
//            $declaration->setId('1');
//            $declaration->setXml($xml, $path);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
        }
    }
}