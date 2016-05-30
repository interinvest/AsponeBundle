<?php
/**
 * Gère les pdf liés aux déclarations AspOne
 */
namespace InterInvest\AsponeBundle\Services;


use InterInvest\AsponeBundle\Entity\DeclarableInterface;
use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\TransactionTCPDFBundle\Lib\TCPDFLib;
use InterInvest\TransactionTCPDFBundle\Transaction\Action\ImportPdfAction;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Crawler;

class AsponePdf
{
    /** @var Container */
    private $container;
    /** @var AsponeXml  */
    private $asponeXml;

    /** @var  string */
    protected $xmlString;
    /** @var  \SimpleXMLElement */
    protected $xml;
    /** @var  string */
    private $rootDir;

    /** @var array  */
    private $forms = array();
    /** @var  Crawler */
    public $crawler;
    /** @var  TCPDFLib */
    private $pdf;


    public function __construct(Container $container, AsponeXml $asponeXml)
    {
        $this->container = $container;
        $this->asponeXml = $asponeXml;
    }

    /**
     * @param Declaration $declaration
     *
     * @throws \Exception
     * @return $this
     */
    public function init($declaration)
    {
        $this->rootDir = $this->container->get('kernel')->getRootDir();

        $this->xmlString = $declaration->getXml($this->container);

        $this->pdf = new TCPDFLib();
        $this->pdf->printFooter = false;

        //vérification des formulaires pdf
        try {
            //tableau des formulaires trouvés pour éviter les doublons de traitement;
            $formUsed = array();

            $this->crawler = new Crawler($this->xmlString);
            $forms = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire");
            /** @var \DOMElement $form */
            foreach ($forms as $form) {
                $formNum = substr($form->getAttribute('Nom'), 0, 4);

                if (!in_array($formNum, $formUsed)) {
                    array_push($formUsed, $formNum);

                    if (!file_exists($this->getTplPath($formNum))) {
                        throw new \Exception("Template pdf inexistant (formulaire $formNum)");
                    }

                    try {
                        //appel methode remplissage
                        $methode = 'setPdf' . $formNum;

                        if (!method_exists($this, $methode)) {
                            throw new \Exception("Méthode $methode() inconnue");
                        }

                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
                    }
                }
            }

            $this->forms = $formUsed;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' ' . $e->getFile() . ' l.' . $e->getLine());
        }

        return $this;
    }

    /**
     * @param $formNum
     *
     * @return string
     */
    private function getTplPath($formNum)
    {
        return __DIR__ . '/../Resources/templates/' . $formNum . '.pdf';
    }

    /**
     * @param string      $name
     * @param Declaration $declaration
     * @param string      $methode
     *
     * @return bool
     */
    public function savePdf($name, $declaration, $methode = 'F')
    {
        $pathSave = $this->rootDir . $this->container->getParameter('aspone.xmlpath');
        if (!file_exists($pathSave) || !is_dir($pathSave)) {
            mkdir($pathSave);
        }

        $pathSave .=  $declaration->getType();

        if (!file_exists($pathSave) || !is_dir($pathSave)) {
            mkdir($pathSave);
        }

        if ($methode == 'F') {
            $this->pdf->Output($pathSave . '/' . $name . '.pdf', 'F');
            return true;
        } else {
            return $this->pdf->Output($name . '.pdf', $methode);
        }
    }

    /**
     * @param $this->pdf
     *
     * @return TCPDFLib
     * @throws \Exception
     */
    public function setPdf3310()
    {
        if (is_null($this->pdf)) {
            $this->pdf = new TCPDFLib();
            $this->pdf->printFooter = false;
        }

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($this->pdf);

        // page 1
        $nomSnc = $this->crawler->filter("Declaration > Redevable > Designation")->first()->text();
        $adresseVoieSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVoie")->first()->text();
        $adresseComplementSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseComplement")->first()->text();
        $codePostalSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseCodePostal")->first()->text();
        $villeSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVille")->first()->text();

        $adresseSnc = $nomSnc.'<br />'.$adresseVoieSnc.' '.$adresseComplementSnc.'<br />'.$codePostalSnc.' '.$villeSnc;

        $referenceDossier = '';
        if($this->crawler->filter("Declaration > ListeDestinataires > Destinataire > ReferenceDossier")->count() == 1) {
            $referenceDossier = $this->crawler->filter("Declaration > ListeDestinataires > Destinataire > ReferenceDossier")->text();
        }

        $identifiantTvaSnc = '';
        if($this->crawler->filter("Declaration > ListeFormulaires > Identif > Zone > Identifiant")->count() == 1) {
            $identifiantTvaSnc = $this->crawler->filter("Declaration > ListeFormulaires > Identif > Zone > Identifiant")->text();
        }

        $neant = '';
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KF > Valeur")->count() == 1) {
            $neant = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KF > Valeur")->text();
        }

        $this->pdf->setPage(1);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $adresseSnc, 'w' => '121', 'h' => '16', 'x' => '80', 'y' => '75', 'align' => 'L '))
            ->add('textOptions', array('size' => 12))
            ->add('html', array('html' => substr($referenceDossier,0,7), 'w' => '32', 'h' => '4', 'x' => '5', 'y' => '105', 'align' => 'C'))
            ->add('html', array('html' => substr($referenceDossier,7,6), 'w' => '26', 'h' => '4', 'x' => '38', 'y' => '105', 'align' => 'C'))
            ->add('html', array('html' => substr($referenceDossier,12,2), 'w' => '8', 'h' => '4', 'x' => '65', 'y' => '105', 'align' => 'C'))
            ->add('html', array('html' => '652', 'w' => '15', 'h' => '4', 'x' => '108', 'y' => '105', 'align' => 'C'))
            ->add('html', array('html' => 'RT', 'w' => '16', 'h' => '4', 'x' => '123', 'y' => '105', 'align' => 'C'))
            ->add('textOptions', array('spacing' => '3.45'))
            ->add('html', array('html' => $identifiantTvaSnc, 'w' => '91', 'h' => '4', 'x' => '17', 'y' => '109.5', 'align' => 'R'))
            ->add('textOptions', array('spacing' => '0'))
            ->add('html', array('html' => $neant ? 'X' : '', 'w' => '5', 'h' => '4', 'x' => '195', 'y' => '136', 'align' => 'L'))
            ->execute();


        // page 2
        $this->pdf->setPage(2);
        $tvaLoyerHT                 = '';
        $tvaAutoLiquide             = '';
        $baseHT                     = '';
        $tvaDue                     = '';
        $totalDue                   = '';
        $tvaDossier                 = '';
        $tvaHonoraire               = '';
        $autreTva                   = '';
        $reportCredit               = '';
        $totalDeductible            = '';
        $TVANPR                     = '';
        $creditTva                  = '';
        $remboursementDemande       = '';
        $creditAReporter            = '';
        $tvaAPayer                  = '';
        $totalAPayer                = '';

        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#CA > Valeur")->count() == 1) {
            $tvaLoyerHT = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#CA > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#CG > Valeur")->count() == 1) {
            $tvaAutoLiquide = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#CG > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#FM > Valeur")->count() == 1) {
            $baseHT = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#FM > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#GM > Valeur")->count() == 1) {
            $tvaDue = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#GM > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#GH > Valeur")->count() == 1) {
            $totalDue = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#GH > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HA > Valeur")->count() == 1) {
            $tvaDossier = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HA > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HB > Valeur")->count() == 1) {
            $tvaHonoraire = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HB > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HC > Valeur")->count() == 1) {
            $autreTva = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HC > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HD > Valeur")->count() == 1) {
            $reportCredit = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HD > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HG > Valeur")->count() == 1) {
            $totalDeductible = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#HG > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KG > Valeur")->count() == 1) {
            $TVANPR = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KG > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JA > Valeur")->count() == 1) {
            $creditTva = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JA > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JB > Valeur")->count() == 1) {
            $remboursementDemande = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JB > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JC > Valeur")->count() == 1) {
            $creditAReporter = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#JC > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KA > Valeur")->count() == 1) {
            $tvaAPayer = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KA > Valeur")->text();
        }
        if($this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KE > Valeur")->count() == 1) {
            $totalAPayer = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#KE > Valeur")->text();
        }

        $this->pdf->transaction()
            ->add('html', array('html' => $tvaLoyerHT, 'w' => '29', 'h' => '3', 'x' => '75', 'y' => '14', 'align' => 'R'))
            ->add('html', array('html' => $tvaAutoLiquide, 'w' => '29', 'h' => '3', 'x' => '75', 'y' => '69.5', 'align' => 'R'))
            ->add('html', array('html' => $baseHT, 'w' => '29', 'h' => '3', 'x' => '145.5', 'y' => '108', 'align' => 'R'))
            ->add('html', array('html' => $tvaDue, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '108', 'align' => 'R'))
            ->add('html', array('html' => $totalDue, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '148', 'align' => 'R'))
            ->add('html', array('html' => $tvaDossier, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '175.5', 'align' => 'R'))
            ->add('html', array('html' => $tvaHonoraire, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '180', 'align' => 'R'))
            ->add('html', array('html' => $autreTva, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '186.5', 'align' => 'R'))
            ->add('html', array('html' => $reportCredit, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '193', 'align' => 'R'))
            ->add('html', array('html' => $totalDeductible, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '203', 'align' => 'R'))
            ->add('html', array('html' => $TVANPR, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '214', 'align' => 'R'))
            ->add('html', array('html' => $creditTva, 'w' => '29', 'h' => '3', 'x' => '76', 'y' => '230', 'align' => 'R'))
            ->add('html', array('html' => $remboursementDemande, 'w' => '29', 'h' => '3', 'x' => '76', 'y' => '241', 'align' => 'R'))
            ->add('html', array('html' => $creditAReporter, 'w' => '29', 'h' => '3', 'x' => '76', 'y' => '264', 'align' => 'R'))
            ->add('html', array('html' => $tvaAPayer, 'w' => '29', 'h' => '3', 'x' => '175.5', 'y' => '232.5', 'align' => 'R'))
            ->add('html', array('html' => $totalAPayer, 'w' => '29', 'h' => '3', 'x' => '172', 'y' => '277.5', 'align' => 'R'))
            ->execute();

        return $this->pdf;
    }

    /**
     * @param $this->pdf
     *
     * @return TCPDFLib
     */
    public function setPdf3519()
    {
        if (is_null($this->pdf)) {
            $this->pdf = new TCPDFLib();
            $this->pdf->printFooter = false;
        }

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3519));
        $import->execute($this->pdf);

        $nomSnc = $this->crawler->filter("Declaration > Redevable > Designation")->first()->text();
        $adresseVoieSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVoie")->first()->text();
        $adresseComplementSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseComplement")->first()->text();
        $codePostalSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseCodePostal")->first()->text();
        $villeSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVille")->first()->text();
        $identifiantTvaSnc = $this->crawler->filter("Declaration > Redevable > Identifiant")->first()->text();
        $entFr = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#FK")->first()->text();
        $nomGerant = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DG > Designation")->text();
        $statutGerant = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DG > DesignationSuite1")->text();
        $sommeDemandee = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DH")->first()->text();
        $croixACrediter = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DD")->first()->text();
        $ville = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DG > AdresseVille")->text();
        $croixDemandeDeposee = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DI")->first()->text();
        $sommeRemboursee = $this->crawler->filter("Declaration > ListeFormulaires > Formulaire > Zone#DN")->first()->text();

        $adresseSnc = $nomSnc.'<br />'.$adresseVoieSnc.'<br />'.$adresseComplementSnc.'<br />'.$codePostalSnc.' '.$villeSnc;

        $this->pdf->setPage(1);
        $this->pdf->transaction()
            ->add('html', array('html' => $adresseSnc, 'w' => '95', 'h' => '25', 'x' => '8', 'y' => '78', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '3.40'))
            ->add('html', array('html' => $identifiantTvaSnc, 'w' => '80.5', 'h' => '4', 'x' => '62', 'y' => '128', 'align' => 'R'))
            ->add('textOptions', array('spacing' => '0'))
            ->add('html', array('html' => $entFr, 'w' => '4', 'h' => '4', 'x' => '181.6', 'y' => '141.5', 'align' => 'L'))
            ->add('html', array('html' => $nomGerant.' '.$statutGerant, 'w' => '80', 'h' => '4', 'x' => '63', 'y' => '181', 'align' => 'L'))
            ->add('html', array('html' => $sommeDemandee, 'w' => '80', 'h' => '4', 'x' => '94', 'y' => '198.5', 'align' => 'L'))
            ->add('html', array('html' => $croixACrediter, 'w' => '4', 'h' => '4', 'x' => '132.3', 'y' => '203', 'align' => 'L'))
            ->add('html', array('html' => $ville, 'w' => '48', 'h' => '4', 'x' => '109', 'y' => '225', 'align' => 'L'))
            ->add('html', array('html' => $croixDemandeDeposee, 'w' => '4', 'h' => '4', 'x' => '18', 'y' => '244.7', 'align' => 'L'))
            ->execute();

        $this->pdf->setPage(3);
        $this->pdf->transaction()
            ->add('html', array('html' => $sommeRemboursee, 'w' => '30', 'h' => '4', 'x' => '171', 'y' => '38', 'align' => 'L'))
            ->execute();

        return $this->pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2031()
    {
        if (is_null($this->pdf)) {
            $this->pdf = new TCPDFLib();
            $this->pdf->printFooter = false;
        }
        $page = $this->pdf->getNumPages() + 1;
        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2031));
        $import->execute($this->pdf);

        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"2031\"] > ";

        $aIdentif = $this->crawler->filter("{$crawlerForm}Zone#HB")->children();
        $identif = '';
        /** @var \DOMElement $zone */
        foreach ($aIdentif as $zone) {
            $identif .= $zone->textContent .  '<br />';
        }
        $aBG = $this->crawler->filter("{$crawlerForm}Zone#BG")->children();
        $adresseSnc = '';
        /** @var \DOMElement $zone */
        foreach ($aBG as $zone) {
            $adresseSnc .= $zone->textContent .  ' ';
        }
        $c7CW = $c7CX = '';
        if ($this->crawler->filter("{$crawlerForm}Zone#CW")->count()) {
            $c7CW = $this->crawler->filter("{$crawlerForm}Zone#CW")->first()->text();
        }
        if ($this->crawler->filter("{$crawlerForm}Zone#CX")->count()) {
            $c7CX = $this->crawler->filter("{$crawlerForm}Zone#CX")->first()->text();
        }


        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('html', array('html' => $identif, 'w' => '90', 'h' => '4', 'x' => '45', 'y' => '65', 'align' => 'L'))
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $adresseSnc, 'w' => '70', 'h' => '4', 'x' => '130', 'y' => '75', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '0', 'size' => 10))
            ->add('html', array('html' => $c7CW . 'dqdqsd', 'w' => '40', 'h' => '4', 'x' => '94', 'y' => '205', 'align' => 'L'))
            ->add('html', array('html' => $c7CX . '', 'w' => '40', 'h' => '4', 'x' => '135', 'y' => '205', 'align' => 'L'))
            ->execute();

        $this->pdf->setPage(2);
        $this->pdf->transaction()
            ->execute();

        return $this->pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2033()
    {
        if (is_null($this->pdf)) {
            $this->pdf = new TCPDFLib();
            $this->pdf->printFooter = false;
        }

        $page = $this->pdf->getNumPages() + 1;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2033));
        $import->execute($this->pdf);

        $this->pdf->setPage($page);

        //Identif
        $crawlerForm = "Declaration > ListeFormulaires > Identif > Zone#AA > ";
        $identif = $this->crawler->filter("{$crawlerForm}Designation")->first()->text();
        $siren = $this->crawler->filter("{$crawlerForm}Identifiant")->first()->text();

        $numero = $this->crawler->filter("{$crawlerForm}AdresseNumero")->count() ? $this->crawler->filter("{$crawlerForm}AdresseNumero")->first()->text() : '';
        $type = $this->crawler->filter("{$crawlerForm}AdresseType")->count() ? $this->crawler->filter("{$crawlerForm}AdresseType")->first()->text() : '';
        $voie = $this->crawler->filter("{$crawlerForm}AdresseVoie")->count() ? $this->crawler->filter("{$crawlerForm}AdresseVoie")->first()->text() : '';
        $complement = $this->crawler->filter("{$crawlerForm}AdresseComplement")->count() ? $this->crawler->filter("{$crawlerForm}AdresseComplement")->first()->text() : '';
        $codePostal = $this->crawler->filter("{$crawlerForm}AdresseCodePostal")->count() ? $this->crawler->filter("{$crawlerForm}AdresseCodePostal")->first()->text() : '';
        $ville = $this->crawler->filter("{$crawlerForm}AdresseVille")->count() ? $this->crawler->filter("{$crawlerForm}AdresseVille")->first()->text() : '';

        $adresse = $numero . $type . ' ' . $voie . ' ' . $complement . ' ' . $codePostal . ' ' . $ville;

        $dateFin = date_create_from_format('Ymd', $this->crawler->filter("Declaration > ListeFormulaires > Identif > Zone#CB")->first()->text());
        $dateDebut = date_create_from_format('Ymd', $this->crawler->filter("Declaration > ListeFormulaires > Identif > Zone#CA")->first()->text());

        $diffMonth = floor(date_diff($dateDebut, $dateFin)->days / 30);

        $this->pdf->transaction()
            ->add('html', array('html' => $identif, 'w' => '90', 'h' => '4', 'x' => '50', 'y' => '22', 'align' => 'L'))
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $adresse, 'w' => '150', 'h' => '4', 'x' => '40', 'y' => '30', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '3.4', 'size' => 12))
            ->add('html', array('html' => $siren, 'w' => '150', 'h' => '4', 'x' => '32', 'y' => '35', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '3.4', 'size' => 10))
            ->add('html', array('html' => $diffMonth, 'w' => '150', 'h' => '4', 'x' => '67', 'y' => '41', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '0'))
            ->execute();

        //2033A
        $nom = '2033A';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }

        $this->pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => isset($JD) ? $JD : '', 'w' => '90', 'h' => '4', 'x' => '195.5', 'y' => '22', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => isset($AC) ? $AC : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => isset($BC) ? $BC : '', 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => isset($CC) ? $CC : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => isset($AE) ? $AE : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => isset($BE) ? $BE : '', 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => isset($CE) ? $CE : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => isset($AJ) ? $AJ : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '110', 'align' => 'L'))
            ->add('html', array('html' => isset($CJ) ? $CJ : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '110', 'align' => 'L'))
            ->add('html', array('html' => isset($AK) ? $AK : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => isset($CK) ? $CK : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => isset($AQ) ? $AQ : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => isset($BQ) ? $BQ : '', 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => isset($CQ) ? $CQ : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => isset($AR) ? $AR : '', 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => isset($BR) ? $BR : '', 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => isset($CR) ? $CR : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => isset($FA) ? $FA : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '157', 'align' => 'L'))
            ->add('html', array('html' => isset($FF) ? $FF : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '179', 'align' => 'L'))
            ->add('html', array('html' => isset($FG) ? $FG : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '191', 'align' => 'L'))
            ->add('html', array('html' => isset($FH) ? $FH : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '196.5', 'align' => 'L'))
            ->add('html', array('html' => isset($FJ) ? $FJ : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '203', 'align' => 'L'))
            ->add('html', array('html' => isset($FL) ? $FL : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '214', 'align' => 'L'))
            ->add('html', array('html' => isset($FM) ? $FM : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '219.5', 'align' => 'L'))
            ->add('html', array('html' => isset($FN) ? $FN : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '225', 'align' => 'L'))
            ->add('html', array('html' => isset($FP) ? $FP : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '230', 'align' => 'L'))
            ->add('html', array('html' => isset($FQ) ? $FQ : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '236', 'align' => 'L'))
            ->add('html', array('html' => isset($FR) ? $FR : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '242', 'align' => 'L'))
            ->add('html', array('html' => isset($FS) ? $FS : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '248', 'align' => 'L'))
            ->execute();
        $page++;

        //2033B
        $nom = '2033B';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => isset($JB) ? $JB : '', 'w' => '90', 'h' => '4', 'x' => '200', 'y' => '12', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => isset($BC) ? $BC : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '37', 'align' => 'L'))
            ->add('html', array('html' => isset($BG) ? $BG : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '60', 'align' => 'L'))
            ->add('html', array('html' => isset($BH) ? $BH : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '66', 'align' => 'L'))
            ->add('html', array('html' => isset($BN) ? $BN : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '94', 'align' => 'L'))
            ->add('html', array('html' => isset($BP) ? $BP : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '100', 'align' => 'L'))
            ->add('html', array('html' => isset($BS) ? $BS : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => isset($BT) ? $BT : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '123', 'align' => 'L'))
            ->add('html', array('html' => isset($BU) ? $BU : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '129', 'align' => 'L'))
            ->add('html', array('html' => isset($BV) ? $BV : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '140', 'align' => 'L'))
            ->add('html', array('html' => isset($BW) ? $BW : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '146', 'align' => 'L'))
            ->add('html', array('html' => isset($BX) ? $BX : '', 'w' => '90', 'h' => '4', 'x' => '85', 'y' => '152', 'align' => 'L'))
            ->add('html', array('html' => isset($BY) ? $BY : '', 'w' => '90', 'h' => '4', 'x' => '85', 'y' => '158', 'align' => 'L'))
            ->add('html', array('html' => isset($BZ) ? $BZ : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '152', 'align' => 'L'))
            ->add('html', array('html' => isset($CA) ? $CA : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '158', 'align' => 'L'))
            ->add('html', array('html' => isset($CC) ? $CC : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '168', 'align' => 'L'))
            ->add('html', array('html' => isset($CD) ? $CD : '', 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '175', 'align' => 'L'))
            ->add('html', array('html' => isset($ED) ? $ED : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '175', 'align' => 'L'))
            ->add('html', array('html' => isset($CF) ? $CF : '', 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '181', 'align' => 'L'))
            ->add('html', array('html' => isset($EL) ? $EL : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '244', 'align' => 'L'))
            ->add('html', array('html' => isset($CM) ? $CM : '', 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '262', 'align' => 'L'))
            ->add('html', array('html' => isset($EM) ? $EM : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '262', 'align' => 'L'))
            ->add('html', array('html' => isset($EP) ? $EP : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '272', 'align' => 'L'))
            ->add('html', array('html' => isset($CR) ? $CR : '', 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '278', 'align' => 'L'))
            ->add('html', array('html' => isset($ER) ? $ER : '', 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '278', 'align' => 'L'))
            ->add('html', array('html' => isset($JA) ? $JA : '', 'w' => '90', 'h' => '4', 'x' => '60', 'y' => '272', 'align' => 'L'))
            ->execute();
        $page++;
        //2033C
        $nom = '2033C';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => isset($RQ) ? $RQ : '', 'w' => '90', 'h' => '4', 'x' => '197.5', 'y' => '19', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => isset($AC) ? $AC : '', 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '53', 'align' => 'L'))
            ->add('html', array('html' => isset($DC) ? $DC : '', 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '53', 'align' => 'L'))
            ->add('html', array('html' => isset($AF) ? $AF : '', 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => isset($BF) ? $BF : '', 'w' => '90', 'h' => '4', 'x' => '86', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => isset($CF) ? $CF : '', 'w' => '90', 'h' => '4', 'x' => '118', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => isset($DF) ? $DF : '', 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => isset($AK) ? $AK : '', 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => isset($BK) ? $BK : '', 'w' => '90', 'h' => '4', 'x' => '86', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => isset($CK) ? $CK : '', 'w' => '90', 'h' => '4', 'x' => '118', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => isset($DK) ? $DK : '', 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => isset($FE) ? $FE : '', 'w' => '90', 'h' => '4', 'x' => '71.5', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => isset($GE) ? $GE : '', 'w' => '90', 'h' => '4', 'x' => '106', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => isset($HE) ? $HE : '', 'w' => '90', 'h' => '4', 'x' => '140', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => isset($JE) ? $JE : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => isset($FH) ? $FH : '', 'w' => '90', 'h' => '4', 'x' => '71.5', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => isset($GH) ? $GH : '', 'w' => '90', 'h' => '4', 'x' => '106', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => isset($HH) ? $HH : '', 'w' => '90', 'h' => '4', 'x' => '140', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => isset($JH) ? $JH : '', 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '151', 'align' => 'L'))
            ->execute();
        $page++;
        //2033D
        $nom = '2033D';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => isset($PF) ? $PF : '', 'w' => '90', 'h' => '4', 'x' => '198.5', 'y' => '36.5', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => isset($PG) ? $PG : '', 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '188', 'align' => 'L'))
            ->add('html', array('html' => isset($PH) ? $PH : '', 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '194', 'align' => 'L'))
            ->add('html', array('html' => isset($PJ) ? $PJ : '', 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '202', 'align' => 'L'))
            ->add('html', array('html' => isset($MG) ? $MG : '', 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '209', 'align' => 'L'))
            ->add('html', array('html' => isset($MH) ? $MH : '', 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '216', 'align' => 'L'))
            ->execute();
        $page++;
        //2033E
        $nom = '2033E';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => isset($DB) ? $DB : '', 'w' => '90', 'h' => '4', 'x' => '189', 'y' => '34', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();
        $page++;
        //2033F
        $nom = '2033F';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->add('html', array('html' => isset($GS) ? $GS : '', 'w' => '90', 'h' => '4', 'x' => '198', 'y' => '23', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();
        $page++;
        //2033G
        $nom = '2033G';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $this->pdf->setPage($page);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->add('html', array('html' => isset($GS) ? $GS : '', 'w' => '90', 'h' => '4', 'x' => '195', 'y' => '19', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();

        return $this->pdf;
    }

    /**
     * @param  $this->pdf
     *
     * @return TCPDFLib
     */
    public function setPdf2083()
    {
        if (is_null($this->pdf)) {
            $this->pdf = new TCPDFLib();
            $this->pdf->printFooter = false;
        }
        $pageOrientation = array(
            1 => 'P',
            2 => 'L',
            3 => 'L',
            4 => 'P',
            5 => 'L',
        );

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2083));
        $import->setOption('orientation', $pageOrientation);
        $import->setOption('pages', array(1));
        $import->execute($this->pdf);

        $crawlerForm = "Declaration > ListeFormulaires > Identif > ";
        $this->pdf->setPage(1);
        $valueCa = $this->crawler->filter("{$crawlerForm}Zone#CA")->first()->text();
        $CA = date_create_from_format('Ymd', $valueCa)->format('dmY');
        $valueCB = $this->crawler->filter("{$crawlerForm}Zone#CB")->first()->text();
        $CB = date_create_from_format('Ymd', $valueCB)->format('dmY');

        $societe = $this->crawler->filter("Declaration > Redevable > Designation")->first()->text();
        if ($this->crawler->filter("Declaration > Redevable > DesignationSuite1")->count()) {
            $societe .= '<br />' . $this->crawler->filter("Declaration > Redevable > DesignationSuite1")->first()->text();
        }

        $adresse = '';
        $fields = array('AdresseNumero', 'AdresseType', 'AdresseVoie', 'AdresseComplement', 'AdresseCodePostal', 'AdresseVille', 'AdresseCodePays');
        foreach ($fields as $field) {
            if ($this->crawler->filter("Declaration > Redevable > Adresse > $field")->count()) {
                $adresse .= $this->crawler->filter("Declaration > Redevable > Adresse > $field")->first()->text();
                if (in_array($field, array('AdresseNumero', 'AdresseType', 'AdresseCodePostal', 'AdresseVille'))) {
                    $adresse .= " ";
                } else {
                    $adresse .= "<br />";
                }
            }
        }

        $siren = $this->crawler->filter("Declaration > Redevable > Identifiant")->first()->text();

        $this->pdf->transaction()
            ->add('textOptions', array('size' => 12, 'spacing' => 3.4))
            ->add('html', array('html' => $CA, 'w' => '90', 'h' => '4', 'x' => '109', 'y' => '118', 'align' => 'L'))
            ->add('html', array('html' => $CB, 'w' => '90', 'h' => '4', 'x' => '158', 'y' => '118', 'align' => 'L'))
            ->add('textOptions', array('spacing' => 0))
            ->add('html', array('html' => $societe, 'w' => '90', 'h' => '4', 'x' => '60', 'y' => '150', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $adresse, 'w' => '90', 'h' => '4', 'x' => '60', 'y' => '170', 'align' => 'L'))
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $siren, 'w' => '90', 'h' => '4', 'x' => '186', 'y' => '175', 'align' => 'L'))
            ->execute();

        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"2083\"] > ";
        $currentPage = 2;

        //associés
        $nbOccurrences = $this->getNombreOccurrences($crawlerForm, 'AA');
        for ($i = 1; $i <= $nbOccurrences; $i++) {
            if(($i%20 == 0 || $i == 1) && $i < $nbOccurrences){
                $max = $i+20;

                $import = new ImportPdfAction();
                $import->setOption('file', $this->getTplPath('aide2083'));
                $import->setOption('orientation', 'P');
                $import->setOption('pages', array(2));
                $import->execute($this->pdf);

                $this->pdf->setPage($currentPage);
                $zones = array(
                    'AA>Designation' => 27,
                    'AA' => array(
                        70,
                        'AdresseNumero',
                        'AdresseType',
                        'AdresseVoie',
                        'AdresseComplement',
                        'AdresseCodePostal',
                        'AdresseVille',
                        'AdresseCodePays'
                    ),
                    'AA>Identifiant' => 115,
                    'AB' => 165,
                );
                $this->setMultiValues($crawlerForm, $zones, 34, 12, 8, $i, $max);
                $currentPage++;
            }
        }

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2083));
        $import->setOption('orientation', $pageOrientation);
        $import->setOption('pages', array(2, 3, 4, 5));
        $import->execute($this->pdf);

        $this->pdf->setPage($currentPage);
        $this->pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->execute();
        $currentPage++;

        $this->pdf->setPage($currentPage);
        $zones = array('BQ' => 48, 'BR' => 74, 'BS' => array(123, 'date' => 'Valeur'), 'BT' => 155, 'BU' => 210);
        $this->setMultiValues($crawlerForm, $zones, 40, 7, 10);
        $currentPage++;

        $this->pdf->setPage($currentPage);
        $zones = array(
            'EA' => 20,
            'EB' => array(25, 'AdresseCodePostal'),
            'ED' => 35,
            'EE' => 42,
            'EF' => 46,
            'EG' => 50,
            'EH' => 55,
            'EJ' => 60,
            'ET' => array(65,'date' => 'Valeur'),
            'EU' => 80,
            'EK' => 100,
            'EL' => array(115, 'date' => 'Valeur'),
            'EM' => array(128, 'date' => 'Valeur'),
            'EN' => array(140, 'date' => 'Valeur'),
            'EV' => 150,
            'EW' => 160,
            'EP' => 185,
        );
        $this->setMultiValues($crawlerForm, $zones, 237, 4.5, 5);
        $currentPage++;

        $this->pdf->setPage($currentPage);
        $zones = array(
            'PA' => array(20, 'int' => 'Valeur'),
            'PB' => array(45, 'int' => 'Valeur'),
            'PC' => array(70, 'int' => 'Valeur'),
            'PD' => array(91, 'int' => 'Valeur'),
            'PE' => array(115, 'int' => 'Valeur'),
            'PF' => array(140, 'int' => 'Valeur'),
            'PG' => array(171, 'int' => 'Valeur'),
            'PH' => array(205, 'int' => 'Valeur'),
            'PJ' => array(241, 'int' => 'Valeur'),
        );
        $this->setMultiValues($crawlerForm, $zones, 43, 7, 10);

        $zones = array(
            'TA>Designation' => 20,
            'TA>Identifiant' => 68,
            'TB' => 95,
            'TA' => array(
                115,
                'AdresseNumero',
                'AdresseType',
                'AdresseVoie',
                'AdresseComplement',
                'AdresseCodePostal',
                'AdresseVille',
                'AdresseCodePays'
            ),
            'TH' => array(180, 'int' => 'Valeur'),
            'TC' => array(210, 'int' => 'Valeur'),
            'TD' => array(250, 'int' => 'Valeur'),
        );
        $this->setMultiValues($crawlerForm, $zones, 100, 4.5, 6);

        return $this->pdf;
    }

    /**
     * @param $type
     * @param $form
     *
     * @return array
     */
    private function getZones($type, $form)
    {
        $dictionnaire = $this->asponeXml->getFormZones($type);
        $return = array();

        foreach ($dictionnaire as $formulaire => $zones) {
            if ($formulaire == $form) {
                $return = array_keys($zones);
                break;
            }
        }
        return $return;
    }

    /**
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param          $crawlerForm
     * @param          $aZones
     * @param          $yOrigin
     * @param          $yIndent
     * @param          $size
     * @param          $minOccurrence
     * @param          $maxOccurrence
     *
     * @throws \Exception
     */
    private function setMultiValues($crawlerForm, $aZones, $yOrigin, $yIndent, $size, $minOccurrence = false, $maxOccurrence = false)
    {
        foreach ($aZones as $id => $item) {
            $y = $yOrigin;
            $i = $minOccurrence ? $minOccurrence : 1;
            $node = '';
            $zone = $id;
            if (strpos($id, '>') !== false) {
                $infosName = explode('>', $id);
                $zone = trim($infosName[0]);
                $node = ' > ' . trim($infosName[1]);
            }

            /** @var \DOMElement $occurrence */
            foreach ($this->crawler->filter("{$crawlerForm}Zone#$zone > Occurrence$node") as $occurrence) {
                if ($maxOccurrence && $i == $maxOccurrence) {
                    continue 2;
                }
                $text = $occurrence->textContent;
                $x = $item;
                if (is_array($item)) {
                    $text = '';
                    $x = $item[0];
                    foreach ($item as $key => $value) {
                        $br = ($id != 'TA');
                        $this->setText($text, $id, $i, $key, $value, $crawlerForm, $br);
                    }
                }
                $this->pdf->transaction()
                    ->add('textOptions', array('size' => $size))
                    ->add('html', array('html' => $text, 'w' => '90', 'h' => '4', 'x' => $x, 'y' => $y, 'align' => 'L'))
                    ->execute()
                ;
                $y += $yIndent;
                $i++;
            }
        }
    }

    /**
     * @param $text
     * @param $id
     * @param $i
     * @param $key
     * @param $value
     * @param $crawlerForm
     * @param $br
     */
    private function setText(&$text, $id, $i, $key, $value, $crawlerForm, $br = true)
    {
        if ($key > 0 && is_int($key)) {
            if ($this->crawler->filter("{$crawlerForm}Zone#$id > Occurrence[Numero=$i] > $value")->count()) {
                $text .= $this->crawler->filter("{$crawlerForm}Zone#$id > Occurrence[Numero=$i] > $value")->first()->text();
                if ($br) {
                    if (in_array($value, array('AdresseNumero', 'AdresseType', 'AdresseCodePostal', 'AdresseVille'))) {
                        $text .= " ";
                    } else {
                        $text .= "<br />";
                    }
                } else {
                    $text .= " ";
                }
            }
        } elseif (is_string($key)) {
            switch ($key) {
                case 'date':
                    $date = date_create_from_format('Ymd', $this->crawler->filter("{$crawlerForm}Zone#$id > Occurrence[Numero=$i] > $value")->first()->text());
                    $text = $date->format('d-m-Y');
                    break;
                case 'int':
                    $text = intval($this->crawler->filter("{$crawlerForm}Zone#$id > Occurrence[Numero=$i] > $value")->first()->text());
                    break;
            }
        }
    }

    /**
     * @param $crawlerForm
     * @param $zone
     *
     * @return int
     */
    private function getNombreOccurrences($crawlerForm, $zone)
    {
        return $this->crawler->filter("{$crawlerForm}Zone#$zone > Occurrence")->count();
    }
}