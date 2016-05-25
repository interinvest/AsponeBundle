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
    /** @var  int */
    private $millesime;

    /** @var array  */
    private $forms = array();
    /** @var  Crawler */
    public $crawler;


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

        $serviceDeclarable = $this->container->get($declaration->getServiceDeclarable()[0]);
        /** @var DeclarableInterface $declarable */
        $declarable = $serviceDeclarable->init($declaration->getServiceDeclarable()[1]);
        $millesime = $declarable->getAnnee() % 100;
        if (date('m') == 1) {
            $millesime--;
        }
        $this->millesime = $millesime;

        $this->xmlString = $declaration->getXml($this->container);
        $this->xml = simplexml_load_string($this->xmlString);

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
     * @param TCPDFLib    $pdf
     * @param string      $name
     * @param Declaration $declaration
     * @param string      $methode
     *
     * @return bool
     */
    public function savePdf($pdf, $name, $declaration, $methode = 'F')
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
            $pdf->Output($pathSave . '/' . $name . '.pdf', 'F');
            return true;
        } else {
            return $pdf->Output($name . '.pdf', $methode);
        }
    }

    /**
     * @return TCPDFLib
     * @throws \Exception
     */
    public function setPdf3310()
    {
        $pdf = new TCPDFLib();
        $pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($pdf);

        // page 1
        $nomSnc = $this->crawler->filter("Declaration > Redevable > Designation")->first()->text();
        $adresseVoieSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVoie")->first()->text();
        $adresseComplementSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseComplement")->first()->text();
        $codePostalSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseCodePostal")->first()->text();
        $villeSnc = $this->crawler->filter("Declaration > Redevable > Adresse > AdresseVille")->first()->text();

        $adresseSnc = $nomSnc.'<br />'.$adresseVoieSnc.'<br />'.$adresseComplementSnc.'<br />'.$codePostalSnc.' '.$villeSnc;

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

        $pdf->setPage(1);
        $pdf->transaction()
            ->add('html', array('html' => $adresseSnc, 'w' => '121', 'h' => '16', 'x' => '80', 'y' => '75', 'align' => 'L '))
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
        $pdf->setPage(2);
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

        $pdf->transaction()
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

        return $pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf3519()
    {
        $pdf = new TCPDFLib();
        $pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3519));
        $import->execute($pdf);

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

        $pdf->setPage(1);
        $pdf->transaction()
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

        $pdf->setPage(3);
        $pdf->transaction()
            ->add('html', array('html' => $sommeRemboursee, 'w' => '30', 'h' => '4', 'x' => '171', 'y' => '38', 'align' => 'L'))
            ->execute();

        return $pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2031()
    {
        $pdf = new TCPDFLib();
        $pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2031));
        $import->execute($pdf);

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
            $c7CW = $this->crawler->filter("{$crawlerForm}Zone#CW")->text();
        }
        if ($this->crawler->filter("{$crawlerForm}Zone#CX")->count()) {
            $c7CX = $this->crawler->filter("{$crawlerForm}Zone#CX")->first()->text();
        }


        $pdf->setPage(1);
        $pdf->transaction()
            ->add('html', array('html' => $identif, 'w' => '90', 'h' => '4', 'x' => '45', 'y' => '65', 'align' => 'L'))
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $adresseSnc, 'w' => '70', 'h' => '4', 'x' => '130', 'y' => '75', 'align' => 'L'))
            ->add('textOptions', array('spacing' => '3.40', 'size' => 10))
            ->add('html', array('html' => $c7CW, 'w' => '40', 'h' => '4', 'x' => '94', 'y' => '205', 'align' => 'R'))
            ->add('textOptions', array('spacing' => '0'))
            ->add('html', array('html' => $c7CX, 'w' => '40', 'h' => '4', 'x' => '135', 'y' => '205', 'align' => 'L'))
            ->execute();

        $pdf->setPage(2);
        $pdf->transaction()
            ->execute();

        return $pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2033()
    {
        $pdf = new TCPDFLib();
        $pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2033));
        $import->execute($pdf);

        //2033A
        $nom = '2033A';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(1);
        $pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $JD, 'w' => '90', 'h' => '4', 'x' => '195.5', 'y' => '22', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $AC, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => $BC, 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => $CC, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '76', 'align' => 'L'))
            ->add('html', array('html' => $AE, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => $BE, 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => $CE, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '87', 'align' => 'L'))
            ->add('html', array('html' => $AJ, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '110', 'align' => 'L'))
            ->add('html', array('html' => $CJ, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '110', 'align' => 'L'))
            ->add('html', array('html' => $AK, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => $CK, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => $AQ, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => $BQ, 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => $CQ, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '139', 'align' => 'L'))
            ->add('html', array('html' => $AR, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => $BR, 'w' => '90', 'h' => '4', 'x' => '145', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => $CR, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '145', 'align' => 'L'))
            ->add('html', array('html' => $FA, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '157', 'align' => 'L'))
            ->add('html', array('html' => $FF, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '179', 'align' => 'L'))
            ->add('html', array('html' => $FG, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '191', 'align' => 'L'))
            ->add('html', array('html' => $FH, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '196.5', 'align' => 'L'))
            ->add('html', array('html' => $FJ, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '203', 'align' => 'L'))
            ->add('html', array('html' => $FL, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '214', 'align' => 'L'))
            ->add('html', array('html' => $FM, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '219.5', 'align' => 'L'))
            ->add('html', array('html' => $FN, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '225', 'align' => 'L'))
            ->add('html', array('html' => $FP, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '230', 'align' => 'L'))
            ->add('html', array('html' => $FQ, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '236', 'align' => 'L'))
            ->add('html', array('html' => $FR, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '242', 'align' => 'L'))
            ->add('html', array('html' => $FS, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '248', 'align' => 'L'))
            ->execute();


        //2033B
        $nom = '2033B';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(2);
        $pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $JB, 'w' => '90', 'h' => '4', 'x' => '200', 'y' => '12', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $BC, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '37', 'align' => 'L'))
            ->add('html', array('html' => $BG, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '60', 'align' => 'L'))
            ->add('html', array('html' => $BH, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '66', 'align' => 'L'))
            ->add('html', array('html' => $BN, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '94', 'align' => 'L'))
            ->add('html', array('html' => $BP, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '100', 'align' => 'L'))
            ->add('html', array('html' => $BS, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '116', 'align' => 'L'))
            ->add('html', array('html' => $BT, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '123', 'align' => 'L'))
            ->add('html', array('html' => $BU, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '129', 'align' => 'L'))
            ->add('html', array('html' => $BV, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '140', 'align' => 'L'))
            ->add('html', array('html' => $BW, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '146', 'align' => 'L'))
            ->add('html', array('html' => $BX, 'w' => '90', 'h' => '4', 'x' => '85', 'y' => '152', 'align' => 'L'))
            ->add('html', array('html' => $BY, 'w' => '90', 'h' => '4', 'x' => '85', 'y' => '158', 'align' => 'L'))
            ->add('html', array('html' => $BZ, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '152', 'align' => 'L'))
            ->add('html', array('html' => $CA, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '158', 'align' => 'L'))
            ->add('html', array('html' => $CC, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '168', 'align' => 'L'))
            ->add('html', array('html' => $CD, 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '175', 'align' => 'L'))
            ->add('html', array('html' => $ED, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '175', 'align' => 'L'))
            ->add('html', array('html' => $CF, 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '181', 'align' => 'L'))
            ->add('html', array('html' => $EL, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '244', 'align' => 'L'))
            ->add('html', array('html' => $CM, 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '262', 'align' => 'L'))
            ->add('html', array('html' => $EM, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '262', 'align' => 'L'))
            ->add('html', array('html' => $EP, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '272', 'align' => 'L'))
            ->add('html', array('html' => $CR, 'w' => '90', 'h' => '4', 'x' => '151', 'y' => '278', 'align' => 'L'))
            ->add('html', array('html' => $ER, 'w' => '90', 'h' => '4', 'x' => '181', 'y' => '278', 'align' => 'L'))
            ->add('html', array('html' => $JA, 'w' => '90', 'h' => '4', 'x' => '60', 'y' => '272', 'align' => 'L'))
            ->execute();

        //2033C
        $nom = '2033C';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(3);
        $pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $RQ, 'w' => '90', 'h' => '4', 'x' => '197.5', 'y' => '19', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $AC, 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '53', 'align' => 'L'))
            ->add('html', array('html' => $DC, 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '53', 'align' => 'L'))
            ->add('html', array('html' => $AF, 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => $BF, 'w' => '90', 'h' => '4', 'x' => '86', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => $CF, 'w' => '90', 'h' => '4', 'x' => '118', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => $DF, 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '70', 'align' => 'L'))
            ->add('html', array('html' => $AK, 'w' => '90', 'h' => '4', 'x' => '57', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => $BK, 'w' => '90', 'h' => '4', 'x' => '86', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => $CK, 'w' => '90', 'h' => '4', 'x' => '118', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => $DK, 'w' => '90', 'h' => '4', 'x' => '148', 'y' => '92', 'align' => 'L'))
            ->add('html', array('html' => $FE, 'w' => '90', 'h' => '4', 'x' => '71.5', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => $GE, 'w' => '90', 'h' => '4', 'x' => '106', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => $HE, 'w' => '90', 'h' => '4', 'x' => '140', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => $JE, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '133', 'align' => 'L'))
            ->add('html', array('html' => $FH, 'w' => '90', 'h' => '4', 'x' => '71.5', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => $GH, 'w' => '90', 'h' => '4', 'x' => '106', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => $HH, 'w' => '90', 'h' => '4', 'x' => '140', 'y' => '151', 'align' => 'L'))
            ->add('html', array('html' => $JH, 'w' => '90', 'h' => '4', 'x' => '175', 'y' => '151', 'align' => 'L'))
            ->execute();

        //2033D
        $nom = '2033D';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(4);
        $pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $PF, 'w' => '90', 'h' => '4', 'x' => '198.5', 'y' => '36.5', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->add('html', array('html' => $PG, 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '188', 'align' => 'L'))
            ->add('html', array('html' => $PH, 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '194', 'align' => 'L'))
            ->add('html', array('html' => $PJ, 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '202', 'align' => 'L'))
            ->add('html', array('html' => $MG, 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '209', 'align' => 'L'))
            ->add('html', array('html' => $MH, 'w' => '90', 'h' => '4', 'x' => '90', 'y' => '216', 'align' => 'L'))
            ->execute();

        //2033E
        $nom = '2033E';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(5);
        $pdf->transaction()
            ->add('textOptions', array('size' => 8))
            ->add('html', array('html' => $DB, 'w' => '90', 'h' => '4', 'x' => '189', 'y' => '34', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();

        //2033F
        $nom = '2033F';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(6);
        $pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->add('html', array('html' => $GS, 'w' => '90', 'h' => '4', 'x' => '198', 'y' => '23', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();

        //2033G
        $nom = '2033G';
        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"$nom\"] > ";

        //traitement simplifié pour valeurs uniques
        foreach ($this->getZones('IDF', $nom) as $zone) {
            $$zone = $this->crawler->filter("{$crawlerForm}Zone#$zone")->count() ? $this->crawler->filter("{$crawlerForm}Zone#$zone")->first()->text() : '';
        }
        $pdf->setPage(7);
        $pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->add('html', array('html' => $GS, 'w' => '90', 'h' => '4', 'x' => '195', 'y' => '19', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();

        return $pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2083()
    {
        $pdf = new TCPDFLib();
        $pdf->printFooter = false;

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
        $import->execute($pdf);

        $crawlerForm = "Declaration > ListeFormulaires > Identif > ";
        $pdf->setPage(1);
        $valueCa = $this->crawler->filter("{$crawlerForm}Zone#CA")->first()->text();
        $CA = date_create_from_format('Ymd', $valueCa)->format('d m Y');
        $valueCB = $this->crawler->filter("{$crawlerForm}Zone#CB")->first()->text();
        $CB = date_create_from_format('Ymd', $valueCB)->format('d m Y');

        $pdf->transaction()
            ->add('textOptions', array('size' => 12))
            ->add('html', array('html' => $CA, 'w' => '90', 'h' => '4', 'x' => '110', 'y' => '118', 'align' => 'L'))
            ->add('html', array('html' => $CB, 'w' => '90', 'h' => '4', 'x' => '168', 'y' => '118', 'align' => 'L'))
            ->add('textOptions', array('size' => 10))
            ->execute();

        $crawlerForm = "Declaration > ListeFormulaires > Formulaire[Nom=\"2083\"] > ";
        $currentPage = 2;

        //associés
        $nbOccurrences = $this->getNombreOccurrences($crawlerForm, 'AA');
        for ($i = 1; $i <= $nbOccurrences; $i++) {
            $max = $i*20+1;
            if($i%20 == 0 || $i == 1){
                $import = new ImportPdfAction();
                $import->setOption('file', $this->getTplPath('aide2083'));
                $import->setOption('orientation', 'P');
                $import->setOption('pages', array(2));
                $import->execute($pdf);

                $pdf->setPage($currentPage++);
            }
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
            $this->setMultiValues($pdf, $crawlerForm, $zones, 34, 12, 8, $max);
        }


        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(2083));
        $import->setOption('orientation', $pageOrientation);
        $import->setOption('pages', array(2, 3, 4, 5));
        $import->execute($pdf);

        $pdf->setPage($currentPage++);
        $pdf->transaction()
            ->add('textOptions', array('size' => 9))
            ->execute();

        $pdf->setPage($currentPage++);
        $zones = array('BQ' => 48, 'BR' => 74, 'BS' => 123, 'BT' => 155, 'BU' => 210);
        $this->setMultiValues($pdf, $crawlerForm, $zones, 40, 7, 10);

        $pdf->setPage($currentPage++);
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
        $this->setMultiValues($pdf, $crawlerForm, $zones, 237, 4.5, 5);

        $pdf->setPage($currentPage++);
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
        $this->setMultiValues($pdf, $crawlerForm, $zones, 43, 7, 10);

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
        $this->setMultiValues($pdf, $crawlerForm, $zones, 100, 4.5, 6);

        return $pdf;
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
            if (substr($formulaire, 0, 4) == $form) {
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
     * TODO prévoir l'intégration entre deux pages d'une page supplémentaire et/ou répétée dans l'import pdf
     * @param TCPDFLib $pdf
     * @param          $crawlerForm
     * @param          $aZones
     * @param          $yOrigin
     * @param          $yIndent
     * @param          $size
     * @param          $maxOccurrence
     *
     * @throws \Exception
     */
    private function setMultiValues(TCPDFLib $pdf, $crawlerForm, $aZones, $yOrigin, $yIndent, $size, $maxOccurrence = false)
    {
        foreach ($aZones as $id => $item) {
            $y = $yOrigin;
            $i = 1;
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
                $pdf->transaction()
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