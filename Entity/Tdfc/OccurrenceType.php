<?php

namespace InterInvest\AsponeBundle\Entity\Tdfc;

/**
 * Class representing OccurrenceType
 *
 *
 * XSD Type: Occurrence
 */
class OccurrenceType
{

    /**
     * @property string $numero
     */
    private $numero = null;

    /**
     * @property string $valeur
     */
    private $valeur = null;

    /**
     * @property string $identifiant
     */
    private $identifiant = null;

    /**
     * @property string $designation
     */
    private $designation = null;

    /**
     * @property string $designationSuite1
     */
    private $designationSuite1 = null;

    /**
     * @property string $designationSuite2
     */
    private $designationSuite2 = null;

    /**
     * @property string $adresseNumero
     */
    private $adresseNumero = null;

    /**
     * @property string $adresseType
     */
    private $adresseType = null;

    /**
     * @property string $adresseVoie
     */
    private $adresseVoie = null;

    /**
     * @property string $adresseComplement
     */
    private $adresseComplement = null;

    /**
     * @property string $adresseHameau
     */
    private $adresseHameau = null;

    /**
     * @property string $adresseCodePostal
     */
    private $adresseCodePostal = null;

    /**
     * @property string $adresseVille
     */
    private $adresseVille = null;

    /**
     * @property string $adresseCodePays
     */
    private $adresseCodePays = null;

    /**
     * @property string $telephone
     */
    private $telephone = null;

    /**
     * @property string $email
     */
    private $email = null;

    /**
     * @property string $texteLibre1
     */
    private $texteLibre1 = null;

    /**
     * @property string $texteLibre2
     */
    private $texteLibre2 = null;

    /**
     * @property string $texteLibre3
     */
    private $texteLibre3 = null;

    /**
     * @property string $texteLibre4
     */
    private $texteLibre4 = null;

    /**
     * @property string $texteLibre5
     */
    private $texteLibre5 = null;

    /**
     * @property string $monnaieCible
     */
    private $monnaieCible = null;

    /**
     * @property string $monnaieSource
     */
    private $monnaieSource = null;

    /**
     * @property string $tauxChange
     */
    private $tauxChange = null;

    /**
     * Gets as numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Sets a new numero
     *
     * @param string $numero
     * @return self
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Gets as valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Sets a new valeur
     *
     * @param string $valeur
     * @return self
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Gets as identifiant
     *
     * @return string
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * Sets a new identifiant
     *
     * @param string $identifiant
     * @return self
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
        return $this;
    }

    /**
     * Gets as designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Sets a new designation
     *
     * @param string $designation
     * @return self
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
        return $this;
    }

    /**
     * Gets as designationSuite1
     *
     * @return string
     */
    public function getDesignationSuite1()
    {
        return $this->designationSuite1;
    }

    /**
     * Sets a new designationSuite1
     *
     * @param string $designationSuite1
     * @return self
     */
    public function setDesignationSuite1($designationSuite1)
    {
        $this->designationSuite1 = $designationSuite1;
        return $this;
    }

    /**
     * Gets as designationSuite2
     *
     * @return string
     */
    public function getDesignationSuite2()
    {
        return $this->designationSuite2;
    }

    /**
     * Sets a new designationSuite2
     *
     * @param string $designationSuite2
     * @return self
     */
    public function setDesignationSuite2($designationSuite2)
    {
        $this->designationSuite2 = $designationSuite2;
        return $this;
    }

    /**
     * Gets as adresseNumero
     *
     * @return string
     */
    public function getAdresseNumero()
    {
        return $this->adresseNumero;
    }

    /**
     * Sets a new adresseNumero
     *
     * @param string $adresseNumero
     * @return self
     */
    public function setAdresseNumero($adresseNumero)
    {
        $this->adresseNumero = $adresseNumero;
        return $this;
    }

    /**
     * Gets as adresseType
     *
     * @return string
     */
    public function getAdresseType()
    {
        return $this->adresseType;
    }

    /**
     * Sets a new adresseType
     *
     * @param string $adresseType
     * @return self
     */
    public function setAdresseType($adresseType)
    {
        $this->adresseType = $adresseType;
        return $this;
    }

    /**
     * Gets as adresseVoie
     *
     * @return string
     */
    public function getAdresseVoie()
    {
        return $this->adresseVoie;
    }

    /**
     * Sets a new adresseVoie
     *
     * @param string $adresseVoie
     * @return self
     */
    public function setAdresseVoie($adresseVoie)
    {
        $this->adresseVoie = $adresseVoie;
        return $this;
    }

    /**
     * Gets as adresseComplement
     *
     * @return string
     */
    public function getAdresseComplement()
    {
        return $this->adresseComplement;
    }

    /**
     * Sets a new adresseComplement
     *
     * @param string $adresseComplement
     * @return self
     */
    public function setAdresseComplement($adresseComplement)
    {
        $this->adresseComplement = $adresseComplement;
        return $this;
    }

    /**
     * Gets as adresseHameau
     *
     * @return string
     */
    public function getAdresseHameau()
    {
        return $this->adresseHameau;
    }

    /**
     * Sets a new adresseHameau
     *
     * @param string $adresseHameau
     * @return self
     */
    public function setAdresseHameau($adresseHameau)
    {
        $this->adresseHameau = $adresseHameau;
        return $this;
    }

    /**
     * Gets as adresseCodePostal
     *
     * @return string
     */
    public function getAdresseCodePostal()
    {
        return $this->adresseCodePostal;
    }

    /**
     * Sets a new adresseCodePostal
     *
     * @param string $adresseCodePostal
     * @return self
     */
    public function setAdresseCodePostal($adresseCodePostal)
    {
        $this->adresseCodePostal = $adresseCodePostal;
        return $this;
    }

    /**
     * Gets as adresseVille
     *
     * @return string
     */
    public function getAdresseVille()
    {
        return $this->adresseVille;
    }

    /**
     * Sets a new adresseVille
     *
     * @param string $adresseVille
     * @return self
     */
    public function setAdresseVille($adresseVille)
    {
        $this->adresseVille = $adresseVille;
        return $this;
    }

    /**
     * Gets as adresseCodePays
     *
     * @return string
     */
    public function getAdresseCodePays()
    {
        return $this->adresseCodePays;
    }

    /**
     * Sets a new adresseCodePays
     *
     * @param string $adresseCodePays
     * @return self
     */
    public function setAdresseCodePays($adresseCodePays)
    {
        $this->adresseCodePays = $adresseCodePays;
        return $this;
    }

    /**
     * Gets as telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Sets a new telephone
     *
     * @param string $telephone
     * @return self
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * Gets as email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets a new email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Gets as texteLibre1
     *
     * @return string
     */
    public function getTexteLibre1()
    {
        return $this->texteLibre1;
    }

    /**
     * Sets a new texteLibre1
     *
     * @param string $texteLibre1
     * @return self
     */
    public function setTexteLibre1($texteLibre1)
    {
        $this->texteLibre1 = $texteLibre1;
        return $this;
    }

    /**
     * Gets as texteLibre2
     *
     * @return string
     */
    public function getTexteLibre2()
    {
        return $this->texteLibre2;
    }

    /**
     * Sets a new texteLibre2
     *
     * @param string $texteLibre2
     * @return self
     */
    public function setTexteLibre2($texteLibre2)
    {
        $this->texteLibre2 = $texteLibre2;
        return $this;
    }

    /**
     * Gets as texteLibre3
     *
     * @return string
     */
    public function getTexteLibre3()
    {
        return $this->texteLibre3;
    }

    /**
     * Sets a new texteLibre3
     *
     * @param string $texteLibre3
     * @return self
     */
    public function setTexteLibre3($texteLibre3)
    {
        $this->texteLibre3 = $texteLibre3;
        return $this;
    }

    /**
     * Gets as texteLibre4
     *
     * @return string
     */
    public function getTexteLibre4()
    {
        return $this->texteLibre4;
    }

    /**
     * Sets a new texteLibre4
     *
     * @param string $texteLibre4
     * @return self
     */
    public function setTexteLibre4($texteLibre4)
    {
        $this->texteLibre4 = $texteLibre4;
        return $this;
    }

    /**
     * Gets as texteLibre5
     *
     * @return string
     */
    public function getTexteLibre5()
    {
        return $this->texteLibre5;
    }

    /**
     * Sets a new texteLibre5
     *
     * @param string $texteLibre5
     * @return self
     */
    public function setTexteLibre5($texteLibre5)
    {
        $this->texteLibre5 = $texteLibre5;
        return $this;
    }

    /**
     * Gets as monnaieCible
     *
     * @return string
     */
    public function getMonnaieCible()
    {
        return $this->monnaieCible;
    }

    /**
     * Sets a new monnaieCible
     *
     * @param string $monnaieCible
     * @return self
     */
    public function setMonnaieCible($monnaieCible)
    {
        $this->monnaieCible = $monnaieCible;
        return $this;
    }

    /**
     * Gets as monnaieSource
     *
     * @return string
     */
    public function getMonnaieSource()
    {
        return $this->monnaieSource;
    }

    /**
     * Sets a new monnaieSource
     *
     * @param string $monnaieSource
     * @return self
     */
    public function setMonnaieSource($monnaieSource)
    {
        $this->monnaieSource = $monnaieSource;
        return $this;
    }

    /**
     * Gets as tauxChange
     *
     * @return string
     */
    public function getTauxChange()
    {
        return $this->tauxChange;
    }

    /**
     * Sets a new tauxChange
     *
     * @param string $tauxChange
     * @return self
     */
    public function setTauxChange($tauxChange)
    {
        $this->tauxChange = $tauxChange;
        return $this;
    }


}

