<?php
/**
 * Uses AspOne's Monitoring service to update object's status
 */

namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\AsponeBundle\Entity\DeclarationHistorique;
use InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail;
use InterInvest\AsponeBundle\Entity\Deposit;
use InterInvest\AsponeBundle\Entity\DepositHistorique;
use InterInvest\AsponeBundle\Entity\DepositHistoriqueDetail;
use InterInvest\AsponeBundle\SoapClient\SoapClient;
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
     * @param Deposit $deposit
     *
     * @return array|bool
     */
    public function setDepositDetails($deposit)
    {
        $this->initSoap();
        /** @var SoapClient $soap */
        $soap = $this->soap;

        $soap->getInterchangesByDepositID($soap->setDepositId($deposit->getIdentifiant()), $soap->setDepositPagination());

        $response = $this->getDepositInformations();

        //traitement !
        if ($response) {
            $deposit->setInterchangeId($response['interchangeId']);
            $deposit->setNumads($response['numADS']);

            $historiqueRepo = $this->em->getRepository('AsponeBundle:DepositHistorique');
            $historiqueDetailRepo = $this->em->getRepository('AsponeBundle:DepositHistoriqueDetail');
            foreach ($historiqueRepo->findBy(array('deposit' => $deposit)) as $historique) {
                foreach ($historiqueDetailRepo->findBy(array('depositHistorique' => $historique)) as $detail) {
                    $this->em->remove($detail);
                }
                $this->em->remove($historique);
            }
            $this->em->flush();
            if (isset($response['historiques'])) {
                foreach ($response['historiques'] as $historique) {
                    $oHistorique = new DepositHistorique();
                    $oHistorique->setDeposit($deposit);
                    $oHistorique->setDate(new \DateTime($historique['date']));
                    $oHistorique->setIserror($historique['isError']);
                    $oHistorique->setIsfinal($historique['isFinal']);
                    $oHistorique->setLabel($historique['label']);
                    $oHistorique->setName($historique['name']);

                    $this->em->persist($oHistorique);

                    if ($historique['isError']) {
                        $deposit->setEtat(Deposit::ETAT_ERREUR);
                    } elseif ($historique['isFinal'] && !$historique['isError']) {
                        $deposit->setEtat(Deposit::ETAT_OK);
                    } elseif ($historique['name'] == 'DEPOSED') {
                        $deposit->setEtat(Deposit::ETAT_OK);
                    }
                    $this->em->persist($deposit);

                    foreach ($historique['detail'] as $detail) {
                        $oDetail = new DepositHistoriqueDetail();
                        $oDetail->setDepositHistorique($oHistorique);
                        $oDetail->setIserror($detail['isError']);
                        $oDetail->setIsfinal($detail['isFinal']);
                        $oDetail->setLabel($detail['label']);
                        $oDetail->setDetail($detail['detailledlabel']);
                        $oDetail->setName($detail['name']);
                        $oDetail->setDate(new \DateTime($detail['date']));
                        $this->em->persist($oDetail);
                    }
                }
            }

            foreach ($response['declarations'] as $declaration) {
                //get declaration
                $this->setDeclarationDetails($declaration->nodeValue);
            }
            $this->em->flush();

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
            $histo['isError'] = $xmlErrors->item($i)->nodeValue == 'true' ? 1 : 0;
            $histo['isFinal'] = $xmlFinals->item($i)->nodeValue == 'true' ? 1 : 0;
            $histo['date'] = $xmlDates->item($i)->nodeValue;
            $histo['detail'] = $this->extraitDetails($xmlHistos->item($i)->lastChild);

            $final['historiques'][$i] = $histo;
        }

        //set des ids declarations
        $final['declarations'] = iterator_to_array($resp->getElementsByTagName("declarationId"));
        $final['numADS'] = $resp->getElementsByTagName("numADS")->item(0) ? $resp->getElementsByTagName("numADS")->item(0)->nodeValue : 0;
        $final['interchangeId'] = $resp->getElementsByTagName("interchangeId")->item(0) ? $resp->getElementsByTagName("interchangeId")->item(0)->nodeValue : 0;

        return $final;
    }

    /**
     * @param string|Declaration $declaration
     *
     * @return mixed
     */
    public function setDeclarationDetails($declaration)
    {
        $identifiant = (is_string($declaration) ? $declaration : $declaration->getIdentifiant());

        $this->initSoap();
        /** @var SoapClient $soap */
        $soap = $this->soap;
        $soap->getDeclarationDetails($identifiant);

        $response = $this->getDeclarationDetailsResponse();
        if ($response) {

            if (isset($response['infos'])) {
                $informations = $response['infos'];
                if (is_string($declaration)) {
                    $identifiant = $informations['identifiant'];
                    $type = $informations['type'];
                    unset($informations['identifiant']);
                    unset($informations['type']);
                    //recherche de la déclaration
                    $declarationRepo = $this->em->getRepository($this->container->getParameter('aspone.declarationRepository'));
                    if (isset($informations['referenceClient']) && $informations['referenceClient']) {
                        $ref = explode('-', $informations['referenceClient']);
                        $id = end($ref);
                        $declarations = $declarationRepo->findBy(array('id' => $id));
                    } else {
                        if (isset($informations['referenceClient'])) {
                            unset($informations['referenceClient']);
                        }
                        $informations['periodeStart'] = new \DateTime($informations['periodeStart']);
                        $informations['periodeEnd'] = new \DateTime($informations['periodeEnd']);

                        $declarations = $declarationRepo->findBy($informations);
                    }
                    foreach ($declarations as $oDeclaration) {
                        if ($oDeclaration->getType() == $type) {
                            $oDeclaration->setIdentifiant($identifiant);
                            $this->em->persist($oDeclaration);
                        }
                    }
                    $this->em->flush();
                } else {
                    $oDeclaration = $declaration;
                }

                $historiqueRepo = $this->em->getRepository('AsponeBundle:DeclarationHistorique');
                $historiqueDetailRepo = $this->em->getRepository('AsponeBundle:DeclarationHistoriqueDetail');

                if (isset($oDeclaration)) {
                    foreach ($historiqueRepo->findBy(array('declarationId' => $oDeclaration->getId())) as $historique) {
                        foreach ($historiqueDetailRepo->findBy(array('declarationHistorique' => $historique)) as $detail) {
                            $this->em->remove($detail);
                        }
                        $this->em->remove($historique);
                    }
                    foreach ($response['historiques'] as $historique) {
                        $oHistorique = new DeclarationHistorique();
                        $oHistorique->setDeclarationId($oDeclaration->getId());
                        $oHistorique->setDate(new \DateTime($historique['date']));
                        $oHistorique->setIserror($historique['isError']);
                        $oHistorique->setIsfinal($historique['isFinal']);
                        $oHistorique->setLabel($historique['label']);
                        $oHistorique->setName($historique['name']);

                        $this->em->persist($oHistorique);


                        if ($historique['isError']) {
                            $oDeclaration->setEtat(Declaration::ETAT_ERREUR);
                        } elseif ($historique['isFinal'] && !$historique['isError']) {
                            $oDeclaration->setEtat(Declaration::ETAT_OK);
                        }
                        $this->em->persist($oDeclaration);

                        foreach ($historique['detail'] as $detail) {
                            $oDetail = new DeclarationHistoriqueDetail();
                            $oDetail->setDeclarationHistorique($oHistorique);
                            $oDetail->setIserror($detail['isError']);
                            $oDetail->setIsfinal($detail['isFinal']);
                            $oDetail->setLabel($detail['label']);
                            if (isset($detail['detailledlabel'])) {
                                $oDetail->setDetail($detail['detailledlabel']);
                            }
                            $oDetail->setName($detail['name']);
                            $oDetail->setDate(new \DateTime($detail['date']));
                            if (isset($detail['codeErreur'])) {
                                $oDetail->setCodeErreur($detail['codeErreur']);
                            }
                            $this->em->persist($oDetail);
                        }
                    }
                }
                $this->em->flush();
            }

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
    public function getDeclarationDetailsResponse($response = null)
    {

        $resp = new \DOMDocument('1.0', 'utf-8');
        if (is_null($response)) {
            /** @var SoapClient $soap */
            $soap = $this->soap;
            $resp->loadXML($soap->parseResponse());
        } else {
            $resp->loadXML($response);
        }

        $XMLresults = $resp->getElementsByTagName("wsResponse");

        if (strpos($XMLresults->item(0)->nodeValue, 'ERROR') === 0) {
            return false;
        }

        $xmlHistos = $resp->getElementsByTagName("stateHistory");

        $final = array();

        if ($resp->getElementsByTagName('rofDeclared')->length) {
            $final['infos'] = array(
                'declarantSiren' => $resp->getElementsByTagName('declarantSiren')->item(0)->nodeValue,
                'periodeStart'   => $resp->getElementsByTagName('periodStart')->item(0)->nodeValue,
                'periodeEnd'     => $resp->getElementsByTagName('periodEnd')->item(0)->nodeValue,
                'identifiant'    => $resp->getElementsByTagName('declarationId')->item(0)->nodeValue,
                'type'           => $resp->getElementsByTagName('code')->item(0)->nodeValue,
                'referenceClient' => $resp->getElementsByTagName('referenceClient')->item(0) ? $resp->getElementsByTagName('referenceClient')->item(0)->nodeValue : ''
            );
        }

        $formulaires = array();
        if ($resp->getElementsByTagName('fiscalForm')->length) {
            for ($i = 0; $i < $resp->getElementsByTagName('fiscalForm')->length; $i++) {
                $name = $resp->getElementsByTagName('fiscalForm')->item($i)->firstChild->textContent;
                if (!strpos($name, 'IDENTIF')) {
                    $formulaires[] = $name;
                }
            }
        }
        sort($formulaires);
        $final['infos']['formulaires'] = implode(',', $formulaires);

        for ($i = 0; $i < $xmlHistos->length; $i++ ) {
            $children = $xmlHistos->item($i)->childNodes;
            foreach ($children as $child) {
                if ($child->nodeName != 'stateDetailsHistory') {
                    if (in_array($child->nodeName, array('isError', 'isFinal'))) {
                        $histo[$child->nodeName] = $child->nodeValue == 'true' ? 1 : 0;
                    } else {
                        $histo[$child->nodeName] = $child->nodeValue;
                    }
                }
            }

            $histo['detail'] = $this->extraitDetails($xmlHistos->item($i)->lastChild);

            $final['historiques'][$i] = $histo;
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
                if (preg_match('/[c|C]ode [e|E]rreur.{1,3}\(?([0-9]{3,7})\)?/', $xmlDetailLabels->item($i)->nodeValue, $matches)) {
                    $histo['codeErreur'] = $matches[1];
                }
            }
            $histo['isError'] = $xmlErrors->item($i)->nodeValue == 'true' ? 1 : 0;
            $histo['isFinal'] = $xmlFinals->item($i)->nodeValue == 'true' ? 1 : 0;
            $histo['date'] = $xmlDates->item($i)->nodeValue;

            $final[$i] = $histo;
        }

        return $final;
    }
}