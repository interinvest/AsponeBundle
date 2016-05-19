<?php
/**
 * Gère les pdf liés aux déclarations AspOne
 */
namespace InterInvest\AsponeBundle\Services;


use InterInvest\AsponeBundle\Entity\DeclarableTvaInterface;
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

    /** @var  TCPDFLib */
    protected $pdf;
    /** @var  string */
    protected $xmlString;
    /** @var  \SimpleXMLElement */
    protected $xml;
    /** @var  string */
    private $rootDir;
    
    /** @var array  */
    public $forms = array();
    /** @var  Crawler */
    public $crawler;


    public function __construct(Container $container, AsponeXml $asponeXml)
    {
        $this->container = $container;
        $this->asponeXml = $asponeXml;
    }

    /**
     * @param Declaration $declaration
     * @param string      $rootDir
     *
     * @throws \Exception
     * @return $this
     */
    public function init($declaration, $rootDir)
    {
        $this->rootDir = $rootDir;
        $this->pdf = new TCPDFLib();

        $path  = $rootDir . $this->container->getParameter('aspone.xmlpath') . $declaration->getXmlPath();
        if ($this->container->getParameter('aspone.archive') == 'yes' && file_exists($path)) {
            $this->xmlString = simplexml_load_file($path)->asXML();
        } else {
            $this->xmlString = $this->asponeXml->setXmlFromDeclarable($declaration->getServiceDeclarable(), 0);
        }

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

    /**     *
     * @return TCPDFLib
     * @throws \Exception
     */
    public function setPdf3310()
    {
        $this->pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($this->pdf);

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

        $this->pdf->setPage(1);
        $this->pdf->transaction()
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
     * @return TCPDFLib
     */
    public function setPdf3519()
    {
        $this->pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
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
        $this->pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($this->pdf);

        return $this->pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2033()
    {
        $this->pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($this->pdf);

        return $this->pdf;
    }

    /**
     * @return TCPDFLib
     */
    public function setPdf2083()
    {
        $this->pdf->printFooter = false;

        $import = new ImportPdfAction();
        $import->setOption('file', $this->getTplPath(3310));
        $import->execute($this->pdf);

        return $this->pdf;
    }

}