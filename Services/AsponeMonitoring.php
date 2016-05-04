<?php
/**
 * Uses AspOne's Monitoring service to update object's status
 */

namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\DepositInterface;
use InterInvest\AsponeBundle\SoapClient\SoapClient;
use ProxyManager\Factory\RemoteObject\Adapter\Soap;
use Symfony\Component\DependencyInjection\Container;

class AsponeMonitoring
{

    private $em;
    private $container;

    private $soap;


    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Initialise le client Soap avec les informations d'authentification
     */
    private function initSoap()
    {
        $options = array(
            'soap_version' => SOAP_1_1,
            'stream_context' => $this->container->getParameter('aspone.context'),
            'authentification' => SOAP_AUTHENTICATION_BASIC,
            'trace' => 1
        );
        $soap = new SoapClient($this->container->getParameter('aspone.wsdl.monitoring'), $options);

        $soap->setContext($this->container->getParameter('aspone.context'));
        $soap->setContextLogin($this->container->getParameter('aspone.contextLogin'));
        $soap->setContextPassword($this->container->getParameter('aspone.contextPassword'));
        $soap->setPassword($this->container->getParameter('aspone.password'));
        $soap->setUsername($this->container->getParameter('aspone.username'));
        $soap->setService($this->container->getParameter('aspone.serviceVersion.0'));
        $soap->setServiceVersion('1.0');
        $soap->setSoapHeaders();
        $soap->__setLocation($this->container->getParameter('aspone.location.monitoring'));

        $this->soap = $soap;
    }

    /**
     * Appelle le WS AspOne récupérant les informations d'un déposit.
     *
     * @param DepositInterface $deposit
     *
     * @return array|bool
     */
    public function setDepositDetails($deposit)
    {
        $this->initSoap();
        /** @var SoapClient $soap */
        $soap = $this->soap;

        $soap->getInterchangesByDepositID($soap->setDepositId($deposit->getIdentifiant()), $soap->setDepositPagination());

        $response = $this->getDepositInformations($soap);

        //traitement !
        if ($response) {
            $deposit->setInterchangeId($response['interchangeId']);
            $deposit->setNumads($response['numADS']);

            return $response;
        } else {
            return false;
        }
    }


    /**
     * Parse la réponse du webservice monitoring pour récupérer les informations du dépôt sous forme de tableau
     *
     * @return array
     */
    public function getDepositInformations()
    {
        /** @var SoapClient $soap */
        $soap = $this->soap;
        $resp = new \DOMDocument('1.0', 'utf-8');
        $resp->loadXML($soap->parseResponse());
        $XMLresults = $resp->getElementsByTagName("wsResponse");

        if (strpos($XMLresults->item(0)->nodeValue, 'ERROR') === 0) {
            return false;
        }

        $xmlHistos = $resp->getElementsByTagName("stateHistory");
        $final = array();

        $xmlNames = $resp->getElementsByTagName("name");
        $xmlLabels = $resp->getElementsByTagName("label");
        $xmlErrors = $resp->getElementsByTagName("isError");
        $xmlFinals = $resp->getElementsByTagName("isFinal");
        $xmlDates = $resp->getElementsByTagName("date");

        for ($i = 0; $i < $xmlHistos->length; $i++ ) {
            $histo['name'] = $xmlNames->item($i+1)->nodeValue; //+1 car premiere node "name" correspond au compte qui a créé l'interchange
            $histo['label'] = $xmlLabels->item($i)->nodeValue;
            $histo['isError'] = $xmlErrors->item($i)->nodeValue;
            $histo['isFinal'] = $xmlFinals->item($i)->nodeValue;
            $histo['date'] = $xmlDates->item($i)->nodeValue;
            $final[$i] = $histo;
        }

        //set des ids declarations
        $final['declarations'] = iterator_to_array($resp->getElementsByTagName("declarationId"));
        $final['numADS'] = $resp->getElementsByTagName("numADS")->item(0) ? $resp->getElementsByTagName("numADS")->item(0)->nodeValue : 0;
        $final['interchangeId'] = $resp->getElementsByTagName("interchangeId")->item(0) ? $resp->getElementsByTagName("interchangeId")->item(0)->nodeValue : 0;

        return $final;
    }

    /**
     * @param $declaration
     *
     * @return mixed
     */
    public function setDeclarationDetails($declaration)
    {
        $this->initSoap();
        /** @var SoapClient $soap */
        $soap = $this->soap;
        $soap->getDeclarationDetails($declaration->getIdentifiant());

        $response = $soap->getDeclarationDetailsResponse();
        if ($response) {

            //renvoi du tableau pour traitement et enregistrement
            return $response;
        } else {
            return false;
        }
    }

    /**
     * Parse la réponse du webservice monitoring pour récupérer les détails de la dernière réponse pour une déclaration
     *
     * @return array
     */
    public function getDeclarationDetailsResponse()
    {
        /** @var SoapClient $soap */
        $soap = $this->soap;

        $resp = new \DOMDocument('1.0', 'utf-8');
        $resp->loadXML($soap->parseResponse());

        $XMLresults = $resp->getElementsByTagName("wsResponse");

        if (strpos($XMLresults->item(0)->nodeValue, 'ERROR') === 0) {
            return false;
        }

        $xmlHistos = $resp->getElementsByTagName("stateHistory");

        $final = array();

        if ($resp->getElementsByTagName('rofDeclared')->length) {
            $rof = $resp->getElementsByTagName('rofDeclared')->item(0)->nodeValue;
            $final['final'] = array(
                $rof,
                $resp->getElementsByTagName('declarantSiren')->item(0)->nodeValue,
                'periodStart' => $resp->getElementsByTagName('periodStart')->item(0)->nodeValue,
                'periodEnd'   => $resp->getElementsByTagName('periodEnd')->item(0)->nodeValue,
                'declarationId' => $resp->getElementsByTagName('declarationId')->item(0)->nodeValue,
                'teleProcedure' => $resp->getElementsByTagName('code')->item(0)->nodeValue
            );
        }

        for ($i = 0; $i < $xmlHistos->length; $i++ ) {
            $children = $xmlHistos->item($i)->childNodes;
            foreach ($children as $child) {
                if ($child->nodeName != 'stateDetailsHistory')
                    $histo[$child->nodeName] = $child->nodeValue;
            }

            $histo['detail'] = $this->extraitDetails($xmlHistos->item($i)->lastChild);

            $final[$i] = $histo;
        }

        return $final;
    }

    /**
     * @param $resp \DOMDocument
     *
     * @return array
     */
    private function extraitDetails($resp)
    {
        if (!$resp) {
            return array();
        }

        $final = array();

        $xmlNames = $resp->getElementsByTagName("name");
        $xmlLabels = $resp->getElementsByTagName("label");
        $xmlDetailLabels = $resp->getElementsByTagName("detailledLabel");
        $xmlErrors = $resp->getElementsByTagName("isError");
        $xmlFinals = $resp->getElementsByTagName("isFinal");
        $xmlDates = $resp->getElementsByTagName("date");

        for ($i = 0; $i < $xmlNames->length; $i++ ) {
            $histo['name'] = $xmlNames->item($i)->nodeValue;
            $histo['label'] = $xmlLabels->item($i)->nodeValue;
            if ($xmlDetailLabels->item($i)) {
                $histo['detailledlabel'] = $xmlDetailLabels->item($i)->nodeValue;
                if (preg_match('/Code Erreur : ([0-9]{3})/', $xmlDetailLabels->item($i)->nodeValue, $matches)) {
                    $histo['codeErreur'] = $matches[1];
                }
            }
            $histo['isError'] = $xmlErrors->item($i)->nodeValue;
            $histo['isFinal'] = $xmlFinals->item($i)->nodeValue;
            $histo['date'] = $xmlDates->item($i)->nodeValue;

            $final[$i] = $histo;
        }

        return $final;
    }
}