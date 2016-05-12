<?php
/**
 * Crée les documents xml utilisés pour un envoi à AspOne (deposit)
 */
namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\Declarable;
use InterInvest\AsponeBundle\Entity\DeclarableInterface;
use InterInvest\AsponeBundle\Entity\DeclarableTdfcInterface;
use InterInvest\AsponeBundle\Entity\DeclarableTvaInterface;
use InterInvest\AsponeBundle\Entity\Declaration;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Yaml\Yaml;

class AsponeXml
{
    private $em;
    private $container;

    private $xml;
    private $declarable;
    private $millesime;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Créer le xml lié à un objet déclarable (déclaration TVA, demande de remboursement, TDFC, etc)
     *
     * @param Declarable $declarable
     * @param Int        $test
     *
     * @return string
     * @throws \Exception
     */
    public function setXmlFromDeclarable($declarable, $test = 1)
    {
        $this->declarable = $declarable;
        $type = $declarable->getType();
        $millesime = $declarable->getAnnee() % 100;
        /*
         * On déclare le dernier trimestre de l'année précédente durant le 1er mois de l'année en cours
         * Donc on prend le millesime précédent
         */
        if (date('m') == 1) {
            $millesime--;
        }
        $this->millesime = $millesime;

        $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='ISO-8859-1'?><XmlEdi Test='$test'></XmlEdi>");
        $groupNode = $rootNode->addChild('GroupeFonctionnel');
        $groupNode->addAttribute('Type', 'INFENT');


        $listeFormNode = $this->setDeclarableGroup($groupNode, $type);

        $this->setFormulaires($listeFormNode);

        try {
            $this->xml = $rootNode->asXml();
            $this->validateXml($type);

            return $this->xml;
        } catch (\Exception $E) {
            throw new \Exception ('Erreur lors de la validation du XML : ' . $E->getMessage(), 0);
        }
    }

    /**
     * Crée le xml pour une déclaration
     * en utilisant un objet declarable qui implémente l'interface voulue
     *
     * @param $node
     *
     * @throws \Exception
     */
    private function setFormulaires(&$node)
    {
        $declarable = $this->declarable;

        try {
            $yml = Yaml::parse(__DIR__ . '/../Resources/millesimes/20' . $this->millesime . '.yml');
            $forms = $yml[$declarable->getType()];
        } catch (\Exception $e) {
            throw new \Exception('Problème lors de la lecture du fichier de millesime.');
        }

        foreach ($forms as $numForm => $zonesForm) {
            $formNode = $node->addChild('Formulaire');
            $formNode->addAttribute('Nom', $numForm);
            $formNode->addAttribute('Millesime', $this->millesime);

            foreach ($zonesForm as $zone) {

                if (method_exists($declarable, 'getMultiple' . $numForm . $zone)) {
                    $getter = 'getMultiple' . $numForm . $zone;
                    if (!is_null($declarable->$getter())) {
                        $zoneX = $formNode->addChild('Zone');
                        $zoneX->addAttribute('id', $zone);
                        $tmp = $declarable->$getter();
                        foreach ($tmp as $i => $vals) {
                            $zoneY = $zoneX->addChild('Occurrence');
                            $zoneY->addAttribute('Numero', ($i + 1));
                            $this->setZones($zoneY, $vals, false);
                        }
                    }
                } elseif (method_exists($declarable, 'get' . $numForm . $zone)) {
                    $getter = 'get' . $numForm . $zone;
                    if (!is_null($declarable->$getter())) {
                        $this->setZones($formNode, array($zone => $declarable->$getter()));
                    }
                }
            }
        }
    }

    /**
     * Crée les groupe fonctionnel de la déclaration en cours
     *
     * @param \SimpleXMLElement $groupNode
     * @param string            $codeDoc
     *
     * @return \SimpleXMLElement
     */
    private function setDeclarableGroup(&$groupNode, $codeDoc)
    {
        /** @var DeclarableInterface $declaration */
        $declaration = $this->declarable;
        $declarationNode = $groupNode->addChild('Declaration');
        $declarationNode->addAttribute('Reference', 'INFENT' . $declaration->getInfent());
        $declarationNode->addAttribute('Type', $codeDoc);

        //création des nodes d'adresses
        $this->setAdresses($declarationNode);

        $listeDestNode = $declarationNode->addChild('ListeDestinataires');
        foreach ($declaration->getDestinataires() as $destinataire) {
            $destNode = $listeDestNode->addChild('Destinataire');
            foreach ($destinataire as $item => $value) {
                $destNode->addChild($item, $value);
            }
        }

        $listeFormNode = $declarationNode->addChild('ListeFormulaires');
        $identifNode = $listeFormNode->addChild('Identif');
        $identifNode->addAttribute('Millesime', $this->millesime);

        //node AA
        $this->setAA($identifNode);
        if ($declaration->getType() == 'TDFC') {
            /** @var DeclarableTdfcInterface $declaration */
            $zones = array(
                'BA' => $declaration->getIdentifBA(),
                'BB' => $declaration->getIdentifBB(),
                'BC' => $declaration->getIdentifBC(),
                'CA' => $declaration->getExercice(),
                'CB' => $declaration->getIdentifCB(),
                'DA' => $declaration->getIdentifDA(),
                'DB' => $declaration->getIdentifDB(),
            );
            $this->setZones($identifNode, $zones);
        }

        if (!is_null($declaration)) {
            //zones identif
            $zones = array(
                'CA' => $declaration->getTIdentifCa(),
                'CB' => $declaration->getTIdentifCb(),
            );
            if ($declaration->getTIdentifEa() && $declaration->getTIdentifHa()) {
                $zones['HA'] = $declaration->getTIdentifHa();
                $zones['GA'] = array(
                    'Iban' => $declaration->getTIdentifGa(),
                    //il faut le bic !
                    'Bic'  => $declaration->getTIdentifGABic(),
                );
                $zones['KA'] = $declaration->getTIdentifKa();
            }
            $zones['KD'] = $declaration->getTIdentifKD();

            $this->setZones($identifNode, $zones);
        }

        return $listeFormNode;
    }

    /**
     * Crée les champs d'adresse du xml
     *
     * @param \SimpleXMLElement $node
     */
    private function setAdresses(&$node)
    {
        /** @var DeclarableInterface $declaration */
        $declaration = $this->declarable;

        //Redacteur
        $redacteurNode = $node->addChild('Redacteur');
        $redacteurNode->addChild('Siret', $declaration->getRedacteurSiret());
        $redacteurNode->addChild('Designation', $declaration->getRedacteurDesignation());
        if ($declaration->getRedacteurDesignationSuite()) {
            $redacteurNode->addChild('DesignationSuite1', $declaration->getRedacteurDesignationSuite());
        }
        $adresseNode = $redacteurNode->addChild('Adresse');
        if ($declaration->getRedacteurAdresseAdresseNumero()) {
            $adresseNode->addChild('AdresseNumero', $declaration->getRedacteurAdresseAdresseNumero());
        }
        $adresseNode->addChild('AdresseVoie', $declaration->getRedacteurAdresseAdresseVoie());
        if ($declaration->getRedacteurAdresseAdresseComplement()) {
            $adresseNode->addChild('AdresseComplement', $declaration->getRedacteurAdresseAdresseComplement());
        }
        $adresseNode->addChild('AdresseCodePostal', $declaration->getRedacteurAdresseAdresseCodePostal());
        $adresseNode->addChild('AdresseVille', $declaration->getRedacteurAdresseAdresseVille());
        $adresseNode->addChild('AdresseCodePays', $declaration->getRedacteurAdresseAdresseCodePays());

        //Redevable
        $redevableNode = $node->addChild('Redevable');
        $redevableNode->addChild('Identifiant', $declaration->getRedevableIdentifiant());
        $redevableNode->addChild('Designation', $declaration->getRedevableDesignation());
        if ($declaration->getRedevableDesignationSuite()) {
            $redevableNode->addChild('DesignationSuite1', $declaration->getRedevableDesignationSuite());
        }

        $adresseNode = $redevableNode->addChild('Adresse');
        if ($declaration->getRedevableAdresseAdresseNumero()) {
            $adresseNode->addChild('AdresseNumero', $declaration->getRedevableAdresseAdresseNumero());
        }
        $adresseNode->addChild('AdresseVoie', $declaration->getRedevableAdresseAdresseVoie());
        if ($declaration->getRedevableAdresseAdresseComplement()) {
            $adresseNode->addChild('AdresseComplement', $declaration->getRedevableAdresseAdresseComplement());
        }
        $adresseNode->addChild('AdresseCodePostal', $declaration->getRedevableAdresseAdresseCodePostal());
        $adresseNode->addChild('AdresseVille', $declaration->getRedevableAdresseAdresseVille());
        $adresseNode->addChild('AdresseCodePays', $declaration->getRedevableAdresseAdresseCodePays());

        $redevableNode->addChild('Rof', $declaration->getTIdentifKD());
    }

    /**
     * @param \SimpleXMLElement $node
     */
    private function setAA(&$node)
    {
        /** @var DeclarableInterface $declaration */
        $declaration = $this->declarable;

        $zoneAaNode = $node->addChild('Zone');
        $zoneAaNode->addAttribute('id', 'AA');
        $zoneAaNode->addChild('Identifiant', $declaration->getIdentifIdentifiant());
        $zoneAaNode->addChild('Designation', $declaration->getIdentifDesignation());
        if ($declaration->getIdentifAdresseAdresseNumero()) {
            $zoneAaNode->addChild('AdresseNumero', $declaration->getIdentifAdresseAdresseNumero());
        }
        $zoneAaNode->addChild('AdresseVoie', $declaration->getIdentifAdresseAdresseVoie());
        if ($declaration->getIdentifAdresseAdresseComplement()) {
            $zoneAaNode->addChild('AdresseComplement', $declaration->getIdentifAdresseAdresseComplement());
        }
        $zoneAaNode->addChild('AdresseCodePostal', $declaration->getIdentifAdresseAdresseCodePays());
        $zoneAaNode->addChild('AdresseVille', $declaration->getIdentifAdresseAdresseVille());
        $zoneAaNode->addChild('AdresseCodePays', $declaration->getIdentifAdresseAdresseCodePays());
        $zoneAaNode->addChild('Email', $declaration->getIdentifEmail());
    }

    /**
     * Parcours le tableau des zones pour créer les champs xml correspondants si les valeurs ne sont pas nulles
     * Prend en compte les valeurs simples et les tableaux
     *
     * @param \SimpleXMLElement $node
     * @param array             $zones
     * @param bool              $setZones
     */
    private function setZones(&$node, array $zones, $setZones = true)
    {
        foreach ($zones as $id => $zone) {
            if (is_null($zone) || !$zone) {
                continue;
            }
            if (is_array($zone)) {
                $no = 0;
                foreach ($zone as $k => $z) {
                    if (!$z) {
                        $no++;
                    }
                }
                if ($no == count($zone)) {
                    continue;
                }
            }
            if ($setZones) {
                $zoneX = $node->addChild('Zone');
                $zoneX->addAttribute('id', $id);
            } else {
                $zoneX = $node;
            }
            if (is_array($zone)) {
                foreach ($zone as $k => $z) {
                    $zoneX->addChild($k, $z);
                }
            } else {
                $zoneX->addChild('Valeur', $zone);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $node
     */
    private function setComments(&$node)
    {
        /** @var DeclarableTvaInterface $declaration */
        $declaration = $this->declarable;

        $zone = $node->addChild('Zone');
        $zone->addAttribute('id', 'FJ');

        $i = 1;
        foreach ($declaration->getFJ() as $value) {
            $zone->{'TexteLibre' . $i} = $value;
            $i++;
        }
    }

    /**
     * Valide la trame selon le xsd correspondant
     *
     * @param $type
     *
     * @return bool
     * @throws \Exception
     */
    private function validateXml($type)
    {
        $xsd = 'Tva';
        if ($type == 'TDFC') {
            $xsd = 'Tdfc';
        }

        $xsdFile = 'XmlEdi' . $xsd . '.xsd';

        $verif = new \DOMDocument();
        $verif->loadXML($this->xml);
        if (!$verif->schemaValidate(__DIR__ . '/../Resources/xsd/' . $xsdFile)) {
            throw new \Exception('XML non valide', 0);
        }
        return true;
    }

    /**
     * Concatène les xml d'une liste de déclarations en un seul document
     *
     * @param array declarations
     * @param int          $test
     *
     * @return string
     */
    public function concatXml(array $declarations, $test = 1)
    {
        $path = $this->container->get('kernel')->getRootDir() . $this->container->getParameter('aspone.xmlPath');
        $xmlContent = "";
        /* @var Declaration $declaration */
        foreach ($declarations as $declaration) {
            $document = $path . '/' . $declaration->getXmlPath();
            if (file_exists($document)) {
                /** @var \SimpleXMLElement $content */
                $content = simplexml_load_file($document);
                $xmlDeclarations = $content->children();
                /** @var \SimpleXMLElement $xmlDeclaration */
                foreach ($xmlDeclarations->children() as $xmlDeclaration) {
                    $xmlContent .= $xmlDeclaration->asXml();
                }
            }
        }
        $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='ISO-8859-1'?>" .
            "<XmlEdi Test='$test'>" .
            "<GroupeFonctionnel Type=\"INFENT\">" .
            $xmlContent .
            "</GroupeFonctionnel>" .
            "</XmlEdi>"
        );

        return utf8_decode($rootNode->saveXML());
    }
}