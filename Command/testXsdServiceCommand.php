<?php

namespace InterInvest\AsponeBundle\Command;

use InterInvest\AsponeBundle\Entity\Tva\GroupeFonctionnelType;
use InterInvest\AsponeBundle\Entity\Tva\XmlEdi;
use InterInvest\AsponeBundle\Entity\Tva\XmlEdiType;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class testXsdServiceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('aspone:test_xsd_service_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serializerBuilder = SerializerBuilder::create();
        $finder = new Finder();
        $ymlDirectory = $finder->files()->in('vendor/interinvest/aspone-bundle/InterInvest/AsponeBundle/Entity')->name('Tva');

        $finder2 = new Finder();
$ymlFiles = $finder2->files()->in('vendor/interinvest/aspone-bundle/InterInvest/AsponeBundle/Resources/xsd')->name('*Tva*');

        foreach ($ymlDirectory->directories() as $directory) {
            $serializerBuilder->addMetadataDir($directory, 'InterInvest\AsponeBundle\Entity\Tva');
        }
        $serializer = $serializerBuilder->build();
        $xml = new XmlEdi();
        $xml->setTest(true);
        $groupe = new GroupeFonctionnelType();
        $groupe->setType("TVA");
        $xml->setGroupeFonctionnel($groupe);


        dump($serializer->serialize($xml, 'xml'));

        $docXml = new \DOMDocument();
        $docXml->loadXML($serializer->serialize($xml, 'xml'));
        foreach($ymlFiles->files() as $file) {

            dump($docXml->schemaValidate($file->getRealPath()));
        }

    }
}
