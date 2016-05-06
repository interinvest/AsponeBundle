<?php

namespace InterInvest\AsponeBundle\Entity;

interface DeclarableInterface
{
    public function getId();

    /**
     * @return string 'TVA', 'RBT', 'TDFC'
     */
    public function getType();

    public function getInfent();
    public function getNumeroFormulaire();

    public function getListeFormulaires();

    public function getAnnee();
    public function getTrimestre();

    public function getTIdentifCA();
    public function getTIdentifCB();
    public function getTIdentifEA();
    public function getTIdentifGA();
    public function getTIdentifHA();
    public function getTIdentifKA();
    public function getTIdentifKD();
    public function getTIdentifGABic();

    public function getDestinataires();

    public function getRedacteurSiret();
    public function getRedacteurDesignation();
    public function getRedacteurDesignationSuite();
    public function getRedacteurAdresseAdresseNumero();
    public function getRedacteurAdresseAdresseVoie();
    public function getRedacteurAdresseAdresseComplement();
    public function getRedacteurAdresseAdresseCodePostal();
    public function getRedacteurAdresseAdresseVille();
    public function getRedacteurAdresseAdresseCodePays();

    public function getRedevableIdentifiant();
    public function getRedevableDesignation();
    public function getRedevableDesignationSuite();
    public function getRedevableAdresseAdresseNumero();
    public function getRedevableAdresseAdresseVoie();
    public function getRedevableAdresseAdresseComplement();
    public function getRedevableAdresseAdresseCodePostal();
    public function getRedevableAdresseAdresseVille();
    public function getRedevableAdresseAdresseCodePays();

    public function getIdentifIdentifiant();
    public function getIdentifDesignation();
    public function getIdentifAdresseAdresseNumero();
    public function getIdentifAdresseAdresseVoie();
    public function getIdentifAdresseAdresseComplement();
    public function getIdentifAdresseAdresseCodePostal();
    public function getIdentifAdresseAdresseVille();
    public function getIdentifAdresseAdresseCodePays();
    public function getIdentifEmail();

    public function getXml();

    /**
     * Récupère tous les xml (tous types confondus) de l'objet déclarable
     * @return mixed
     */
    public function getXmls();
}
