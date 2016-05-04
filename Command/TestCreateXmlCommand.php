<?php

namespace InterInvest\AsponeBundle\Command;

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
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Chemin du répertoire de sauvegarde du fichier de test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('aspone.services.xml');
        $declaration = new TestDeclarableTva();

        try {
            $xml = new \SimpleXMLElement($service->setXmlFromDeclarable($declaration));

            $path = $input->getOption('path');
            if (!file_exists($path) || !is_dir($path)) {
                mkdir($path);
            }

            $xml->saveXML($path . '/testDeclaration' . $declaration->getType() . '.xml');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}