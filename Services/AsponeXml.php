<?php
/**
 * Crée les documents xml utilisés pour un envoi à AspOne (deposit)
 */
namespace InterInvest\AsponeBundle\Services;

use Doctrine\ORM\EntityManager;
use InterInvest\AsponeBundle\Entity\Declarable;
use InterInvest\AsponeBundle\Entity\DeclarableInterface;
use InterInvest\AsponeBundle\Entity\Declaration;
use Symfony\Component\DependencyInjection\Container;

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
     * @param Declarable|Array $declarable si array alors {service declarable, entité}
     * @param array $formulaires
     * @param int   $test
     *
     * @return mixed
     * @throws \Exception
     */
    public function setXmlFromDeclarable($declarable, $formulaires = array(), $test = 1)
    {
        if (is_array($declarable)) {
            if (!isset($declarable[1])) {
                throw new \Exception('Entité manquante pour l\'appel au service ' . $declarable[0]);
            }
            $serviceDeclarable = $this->container->get($declarable[0]);
            $declarable = $serviceDeclarable->init($declarable[1], $formulaires);
        }
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

        $this->setFormulaires($listeFormNode, $formulaires);
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
     * @param       $node
     * @param array $formulaires
     *
     * @throws \Exception
     */
    private function setFormulaires(&$node, $formulaires = array())
    {
        /** @var DeclarableInterface $declarable */
        $declarable = $this->declarable;

        try {
            $forms = $this->getFormZones($declarable->getType());
        } catch (\Exception $e) {
            throw new \Exception('Problème lors de la lecture du fichier de millesime.');
        }

        /**
         * Test des formulaires dans la déclaration
         * Si présents dans le dictionnaire alors on les rempli
         */
        $formulaires = explode(',', $formulaires);
        array_unshift($formulaires, 'Identif');

        $formulairesDeclarables = $declarable->getConfiguration();

        foreach ($formulairesDeclarables as $formulaire => $zones) {
            if (!in_array($formulaire, $formulaires)) {
                continue;
            }
            if (isset($forms[$formulaire])) {
                if ($formulaire != 'Identif') {
                    $formNode = $node->addChild('Formulaire');
                    $formNode->addAttribute('Nom', $formulaire);
                } else {
                    $formNode = $node->addChild('Identif');
                }
                $formNode->addAttribute('Millesime', $this->millesime);
                if ($formulaire == 'Identif') {
                    $this->setAA($formNode);
                }
                foreach ($zones as $zone => $val) {
                    if (isset($forms[$formulaire][$zone])) {
                        $value = $this->getValue($declarable->getConfiguration(), $formulaire, $zone);
                        if (!is_null($value)) {
                            if ($forms[$formulaire][$zone]['multi'] == 'NON') {
                                //$setZones = ($forms[$formulaire][$zone]['retour'] == 'value' ? true : false);
                                $this->setZones($formNode, array($zone => $value));
                            } else {
                                $zoneX = $formNode->addChild('Zone');
                                $zoneX->addAttribute('id', $zone);
                                $tmp = $value;
                                $i = 1;
                                foreach ($tmp as $vals) {
                                    $zoneY = $zoneX->addChild('Occurrence');
                                    $zoneY->addAttribute('Numero', $i);
                                    $this->setZones($zoneY, (array)$vals, false);
                                    $i++;
                                }
                            }
                        }
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
        $this->setZones($redacteurNode, $declaration->getRedacteur(), true, true);
        //Redevable
        $redevableNode = $node->addChild('Redevable');
        $this->setZones($redevableNode, $declaration->getRedevable(), true, true);
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
        $this->setZones($zoneAaNode, $declaration->getIdentif(), true, true);
    }

    /**
     * Parcours le tableau des zones pour créer les champs xml correspondants si les valeurs ne sont pas nulles
     * Prend en compte les valeurs simples et les tableaux
     *
     * @param \SimpleXMLElement $node
     * @param                   $zones
     * @param bool              $setZones
     * @param bool              $isSimple La zone envoyée est-elle simple ? (cad balise correspond à id et non <balise id=id>)
     */
    private function setZones(&$node, $zones, $setZones = true, $isSimple = false)
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
            if ($setZones && !$isSimple) {
                $zoneX = $node->addChild('Zone');
                $zoneX->addAttribute('id', $id);
            } elseif ($setZones && $isSimple && is_array($zone)) {
                $zoneX = $node->addChild($id);
            } else {
                $zoneX = $node;
            }
            if (is_array($zone)) {
                foreach ($zone as $k => $z) {
                    if (!is_null($z) && $z != '') {
                        if (is_array($z) && isset($z['Valeur']) && $z['Valeur']) {
                            $zoneX->addChild('Valeur', $z['Valeur']);
                        } elseif (!is_array($z) && $z) {
                            $zoneX->addChild($k, $z);
                        }
                    }
                }
            } elseif ($setZones && $isSimple) {
                if (!is_null($zone) && $zone != '') {
                    $zoneX->addChild($id, $zone);
                }
            } else {
                if (!is_null($zone) && $zone != '') {
                    $zoneX->addChild('Valeur', $zone);
                }
            }
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
        if ($type == 'IDF') {
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
            $document = $declaration->getXml($this->container);
            if ($document) {
                /** @var \SimpleXMLElement $content */
                $content = simplexml_load_string($document);
                $xmlDeclarations = $content->children();
                /** @var \SimpleXMLElement $xmlDeclaration */
                foreach ($xmlDeclarations->children() as $xmlDeclaration) {
                    $xmlContent .= $xmlDeclaration->asXml();
                }
                if ($this->container->getParameter('aspone.archive') == 'yes') {
                    $declaration->archiveXml($document, $path);
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

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    private function getFormZones($type)
    {
        $formsZones = array();

        if (in_array($type, array('RBT', 'IDT'))) {
            $typeDepot = 'TVA';
        } elseif (in_array($type, array('IDF'))) {
            $typeDepot = 'TDFC';
        }

        $path = __DIR__ . '/../Resources/dictionnaires/';

        try {
            $dir = opendir($path);
            while (false !== ($fichier = readdir($dir))) {
                if (strpos($fichier, 'DICTIONNAIRE_' . $typeDepot . '_') !== false) {
                    if (strpos($fichier, (string)$this->millesime) !== false) {
                        $handle = fopen($path . $fichier, 'r');
                        $row = 0;
                        while (($data = fgetcsv($handle, 100000, ";")) !== false) {
                            if ($row > 0) {
                                $formulaire = $data[0];
                                if (strpos($data[0], 'IDENTIF') !== false) {
                                    $formulaire = 'Identif';
                                }
                                if ($data[1] == $this->millesime) {
                                    $formsZones[$formulaire][$data[2]] = array(
                                        'multi'  => $data[5],
                                        'retour' => $data[7] == 'Valeur' ? 'value' : 'array',
                                    );
                                }
                            }
                            $row++;
                        }
                        fclose($handle);
                    }
                }
            }
            closedir($dir);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
        }
        return $formsZones;
    }

    /**
     * @param array $declarableConfig
     * @param       $form
     * @param       $zone
     *
     * @return mixed
     */
    private function getValue(Array $declarableConfig, $form, $zone)
    {
        if (isset($declarableConfig[$form][$zone]))
        {
            return $declarableConfig[$form][$zone];
        }
        return null;
    }
}