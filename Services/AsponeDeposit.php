<?php
/**
 * Uses AspOne's service to create a deposit
 */
namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\Declarable;
use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\AsponeBundle\Entity\DeclarationInterface;
use InterInvest\AsponeBundle\SoapClient\SoapClient;
use InterInvest\AsponeBundle\SoapClient\SoapClientBuilder;
use Symfony\Component\DependencyInjection\Container;

class AsponeDeposit
{

    private $em;
    private $container;
    private $builder;

    /** @var SoapClient soap */
    public $soap;

    public function __construct(EntityManager $em, Container $container, SoapClientBuilder $builder)
    {
        $this->em = $em;
        $this->container = $container;
        $this->builder = $builder;
    }

    /**
     * Crée un tableau de déclarations à enregistrer pour utiliser ensuite dans le déposit
     *
     * @param DeclarationInterface $declaration
     *
     * @return array
     */
    public function createDeclarationFromDeclarable(DeclarationInterface $declaration)
    {
        $declarations = array();

        $aDeclaration = array(
            'etat' => 0,
            'type' => $declaration->getType(),
            'declarableId' => $declaration->getId(),
            'xml' => $declaration->getXml($declaration->getType())
        );

        $declarations[] = $aDeclaration;

        if ($declaration->getType() != 'RBT' && $declaration->getDeclarable()->get3310CA3JB() > 0) {
            $aDeclaration = array(
                'etat' => 0,
                'type' => 'RBT',
                'declarableId' => $declaration->getDeclarable()->getId(),
                'xml' => $declaration->getXml('RBT')
            );

            $declarations[] = $aDeclaration;
        }

        return $declarations;
    }


    /**
     * Crée un tableau de dépôts à partir d'une liste de declarations
     *
     * @param array  $declarations
     * @param int    $test
     *
     * @return array
     */
    public function createDeposit(array $declarations, $test = 1)
    {
        $asponeXml = $this->container->get('aspone.services.xml');
        $deposits = array();

        /**
         * Il faut un maximum de 100 déclarations par deposit
         */
        $declarationsCh = array_chunk($declarations, 100);

        foreach ($declarationsCh as $k => $declarations) {
            $deposit = array(
                'declaratons' => array(),
            );
            $deposit['xml'] = $asponeXml->concatXml($declarations, $test);

            /** @var DeclarationInterface $declaration */
            foreach ($declarations as $declaration) {
                $deposit['declarations'][] = array($declaration->getId(), $declaration->getType());
            }
            $deposits[] = $deposit;
        }

        return $deposits;
    }

    /**
     * Initialise le client Soap avec les informations d'authentification
     */
    private function initSoap()
    {
        $this->builder->withMtomAttachments();
        $this->builder->withTrace(true);
        $this->builder->withWsdl($this->container->getParameter('aspone.wsdl.teledeclarations'));

        $soap = $this->builder->build();
        $soap->setContextLogin($this->container->getParameter('aspone.contextLogin'));
        $soap->setContextPassword($this->container->getParameter('aspone.contextPassword'));
        $soap->setPassword($this->container->getParameter('aspone.password'));
        $soap->setUsername($this->container->getParameter('aspone.username'));
        $soap->setServiceVersion('1.1');
        $soap->setContext($this->container->getParameter('aspone.context'));
        $soap->setService($this->container->getParameter('aspone.serviceVersion.1'));
        $soap->setSoapHeaders();
        $soap->__setLocation($this->container->getParameter('aspone.location.teledeclarations'));

        $this->soap = $soap;
    }

    /**
     * @param $request
     * @param $type
     *
     * @return string
     * @throws \Exception
     */
    public function sendDeposit($request, $type)
    {
        $req = $request->asXML();
        $this->initSoap();
        $this->soap->addDocument('Depot ' . $type, $type, $req);

        $response = $this->soap->getResponse();

        if ($response) {
            return $response;
        } else {
            throw  new \Exception("Réponse ASPONE vide");
        }

    }
}