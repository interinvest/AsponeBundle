<?php
/**
 * Uses AspOne's service to create a deposit
 */
namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\Declarable;
use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\AsponeBundle\Entity\Deposit;
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
     * Crée un tableau de dépôts à partir d'une liste de declarations
     *
     * @param array  $declarations
     * @param string $type
     * @param int    $test
     * @param int    $limit
     *
     * @return int
     */
    public function createDeposit(array $declarations, $type, $test = 1, $limit = 100)
    {
        $asponeXml = $this->container->get('aspone.services.xml');
        $deposits = 0;


        /**
         * Il faut un maximum de 100 déclarations par deposit
         */
        if ($limit > 100) {
            $limit = 100;
        }
        $declarationsCh = array_chunk($declarations, $limit);

        foreach ($declarationsCh as $k => $declarations) {
            $xml = $asponeXml->concatXml($declarations, $test);
            if ($xml) {
                $oDeposit = new Deposit();
                $oDeposit->setType($type);
                $oDeposit->setEtat(0);
                $oDeposit->setRetourImmediat(Deposit::ETAT_NON_FINI);
                $oDeposit->setIstest($test);
                $this->em->persist($oDeposit);
                $this->em->flush(); //flush pour donner l'id aux declarations ensuite

                $deposits++;
                echo $deposits . "\n";
                /** @var Declaration $declaration */
                foreach ($declarations as $declaration) {
                    $declaration->setDepositId($oDeposit->getId());
                }
            }
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
     * @param         $request
     * @param Deposit $deposit
     *
     * @return string
     * @throws \Exception
     */
    public function sendDeposit($request, Deposit $deposit)
    {
        $req = ($request instanceof \SimpleXMLElement) ? $request->asXML() : $request;
        $this->initSoap();
        $this->soap->addDocument('Depot ' . $deposit->getType(), $deposit->getType(), $req);

        $response = $this->soap->getResponse();

        if ($response) {
            $deposit->setDateEnvoi(new \DateTime());
            $deposit->setRetourImmediat(strpos($response, 'SUCCESS') === 0 ? Deposit::ETAT_OK : Deposit::ETAT_ERREUR);
            $identif = str_replace(array('SUCCESS', 'ERROR'), '', $response);
            $deposit->setIdentifiant($identif);
            $deposit->setEtat(Deposit::ETAT_NON_FINI);

            return $response;
        } else {
            throw  new \Exception("Réponse ASPONE vide");
        }

    }
}