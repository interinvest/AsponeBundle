<?php

namespace InterInvest\AsponeBundle\Test;

use InterInvest\AsponeBundle\Entity\DeclarableTdfcInterface;

class TestDeclarableTdfc implements DeclarableTdfcInterface
{
    public function getXml(){
        $type = $this->getType();
        return '';
    }

    public function getId()
    {
        return 192046;
    }

    public function getType()
    {
        return 'TVA';
    }

    public function getInfent()
    {
        return 'INFENTT9020161-192046';
    }

    public function getNumeroFormulaire()
    {
        return '3310CA3';
    }

    public function getListeFormulaires()
    {
        $zones = array(
            'CA', 'GM', 'KA', 'KE', 'JC', 'JB', 'JA', 'CG', 'HA', 'HB', 'HC', 'HD', 'GH', 'FM', 'HG', 'KG', 'KF',
        );
        return array('3310CA3' => $zones);
    }

    public function getAnnee()
    {
        return 2016;
    }

    public function getTrimestre()
    {
        return 1;
    }

    public function getTIdentifCA()
    {
        return '20160101';
    }

    public function getTIdentifCB(){ return '20160331'; }
    public function getTIdentifEA(){ return false; }
    public function getTIdentifGA(){ return false; }
    public function getTIdentifHA(){ return false; }
    public function getTIdentifKA(){ return false; }
    public function getTIdentifKD(){ return 'TVA1'; }
    public function getTIdentifGABic(){ return false; }

    public function getDestinataires()
    {
        return array(
            0 => array(
                'Designation' => 'DGI_EDI_TVA'
            )
        );
    }

    public function getRedacteurSiret(){ return '38384866000106'; }
    public function getRedacteurDesignation(){ return 'ENT_EDI_TVA'; }
    public function getRedacteurDesignationSuite(){ return 'Inter Invest'; }
    public function getRedacteurAdresseAdresseNumero(){ return '40'; }
    public function getRedacteurAdresseAdresseVoie(){ return 'rue de Courcelles'; }
    public function getRedacteurAdresseAdresseComplement(){ return false; }
    public function getRedacteurAdresseAdresseCodePostal(){ return '75008'; }
    public function getRedacteurAdresseAdresseVille(){ return 'PARIS'; }
    public function getRedacteurAdresseAdresseCodePays(){ return 'FR'; }

    public function getRedevableIdentifiant(){ return '817695745'; }
    public function getRedevableDesignation(){ return 'TERNES 90'; }
    public function getRedevableDesignationSuite(){ return false; }
    public function getRedevableAdresseAdresseNumero(){ return false; }
    public function getRedevableAdresseAdresseVoie(){ return 'Immeuble AGORA, Bâtiment C,'; }
    public function getRedevableAdresseAdresseComplement(){ return 'Etang Z\'abricots'; }
    public function getRedevableAdresseAdresseCodePostal(){ return '97200'; }
    public function getRedevableAdresseAdresseVille(){ return 'FORT DE FRANCE'; }
    public function getRedevableAdresseAdresseCodePays(){ return 'FR'; }

    public function getIdentifIdentifiant(){ return '817695745'; }
    public function getIdentifDesignation(){ return 'TERNES 90'; }
    public function getIdentifAdresseAdresseNumero(){ return false; }
    public function getIdentifAdresseAdresseVoie(){ return 'Immeuble AGORA, Bâtiment C,'; }
    public function getIdentifAdresseAdresseComplement(){ return 'Etang Z\'abricots'; }
    public function getIdentifAdresseAdresseCodePostal(){ return '97200'; }
    public function getIdentifAdresseAdresseVille(){ return 'FORT DE FRANCE'; }
    public function getIdentifAdresseAdresseCodePays(){ return 'FR'; }
    public function getIdentifEmail(){ return 'impots@inter-invest.fr'; }

    public function get3310CA3CA(){ return false; }
    public function get3310CA3CG(){ return false; }
    public function get3310CA3GM(){ return false; }
    public function get3310CA3GH(){ return false; }
    public function get3310CA3HA(){ return false; }
    public function get3310CA3HB(){ return false; }
    public function get3310CA3HC(){ return false; }
    public function get3310CA3HD(){ return false; }
    public function get3310CA3JA(){ return false; }
    public function get3310CA3JB(){ return false; }
    public function get3310CA3JC(){ return false; }
    public function get3310CA3KA(){ return false; }
    public function get3310CA3KF(){ return 'X'; }
    public function get3310CA3KE(){ return false; }

    /**
     * CA + CG
     * @return mixed
     */
    public function get3310CA3FM(){ return false; }

    /**
     * HA + HB + HC + HD
     * @return mixed
     */
    public function get3310CA3HG(){ return false; }

    /**
     * TVA npr
     * @return mixed
     */
    public function get3310CA3KG(){ return false; }


    /**
     * @return mixed
     */
    public function get3519DH(){ return false; }
    public function get3519DN(){ return false; }
    public function get3519FK(){ return false; }
    public function get3519DD(){ return false; }
    public function get3519DI(){ return false; }

    public function get3519AA()
    {
//        return array(
//            'Iban' => '',
//            'BIC'  => '',
//            'TitulaireDesignation' => ''
//        );
        return false;
    }

    /**
     * @return array TexteLibre[$i]
     */
    public function get3519FJ(){ return false; }

    public function get3519DC()
    {
//        return array(
//            'Designation' => '',
//            'DesignationSuite' => '',
//            'AdresseNumero' => '',
//            'AdresseVoie' => '',
//            'AdresseComplement' => '',
//            'AdresseCodePostal' => '',
//            'AdresseVille' => '',
//            'AdresseCodePays' => '',
//        );
        return false;
    }

    public function get3519DG()
    {
//        return array(
//            'Designation' => '',
//            'DesignationSuite' => '',
//            'DesignationAdresseVille' => '',
//        );
        return false;
    }
}
