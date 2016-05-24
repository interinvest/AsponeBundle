<?php

namespace InterInvest\AsponeBundle\Entity;

use Symfony\Component\DependencyInjection\Container;

/**
 * AsponeDeclaration
 */
abstract class Declaration
{
    const TYPE_RBT  = 'RBT';
    const TYPE_IDT  = 'IDT';
    const TYPE_IDF  = 'IDF';

    const ETAT_NON_FINIE = 0;
    const ETAT_OK = 1;
    const ETAT_ERREUR = 2;

    public static $correspondancesTypes = array(
        'TVA'  => array('IDT', 'RBT'),
        'TDFC' => array('CVA', 'CRM', 'IAT', 'IDF', 'ILF', 'LIS', 'LOY'),
    );

    public static $formPdf = array(
        'IDT' => array(3310),
        'RBT' => array(3519),
        'IDF' => array(2033, 2031, 2083)
    );

    abstract function getId();

    abstract function setType($type);
    abstract function getType();

    abstract function getIdentifiant();
    abstract function setIdentifiant($identifant);

    abstract function getEtat();
    abstract function setEtat($etat);

    abstract function getDeposit();
    abstract function setDeposit($deposit);

    abstract function getDeclarantSiren();
    abstract function setDeclarantSiren($declarantSirent);

    abstract function getPeriodeStart();
    abstract function setPeriodeStart($periodeStart);

    abstract function getPeriodeEnd();
    abstract function setPeriodeEnd($periodeEnd);

    abstract function archiveXml($xml, $path);

    abstract function setReferenceClient($referenceClient);
    abstract function getReferenceClient();

    abstract function setFormulaires($formulaires);
    abstract function getFormulaires();

    /**
     * Doit renvoyer un tableau avec le nom du service pour la création de l'objet déclarable et l'entité utilisée
     * @return mixed
     */
    abstract function getServiceDeclarable();

    /**
     * @return string
     */
    public function getXmlPath()
    {
        return $this->getType() . '/' . $this->getId() . '.xml';
    }

    /**
     * @param Container $container
     *
     * @return string
     */
    public function getXml(Container $container)
    {
        if ($container->getParameter('aspone.archive') == 'yes') {
            $path = $container->get('kernel')->getRootDir() . $container->getParameter('aspone.xmlPath') . $this->getXmlPath();
            if (file_exists($path)) {
                return file_get_contents($path);
            }
        }

        $serviceXml = $container->get('aspone.services.xml');
        return $serviceXml->setXmlFromDeclarable($this->getServiceDeclarable(), 1, $this->getFormulaires());
    }
}