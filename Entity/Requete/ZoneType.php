<?php

namespace InterInvest\AsponeBundle\Entity\Requete;

/**
 * Class representing ZoneType
 *
 *
 * XSD Type: Zone
 */
class ZoneType
{

    /**
     * @property string $id
     */
    private $id = null;

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
     * @property \InterInvest\AsponeBundle\Entity\Requete\OccurrenceType[] $occurrence
     */
    private $occurrence = array(
        
    );

    /**
     * Gets as id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a new id
     *
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Adds as occurrence
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Requete\OccurrenceType $occurrence
     */
    public function addToOccurrence(\InterInvest\AsponeBundle\Entity\Requete\OccurrenceType $occurrence)
    {
        $this->occurrence[] = $occurrence;
        return $this;
    }

    /**
     * isset occurrence
     *
     * @param scalar $index
     * @return boolean
     */
    public function issetOccurrence($index)
    {
        return isset($this->occurrence[$index]);
    }

    /**
     * unset occurrence
     *
     * @param scalar $index
     * @return void
     */
    public function unsetOccurrence($index)
    {
        unset($this->occurrence[$index]);
    }

    /**
     * Gets as occurrence
     *
     * @return \InterInvest\AsponeBundle\Entity\Requete\OccurrenceType[]
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    /**
     * Sets a new occurrence
     *
     * @param \InterInvest\AsponeBundle\Entity\Requete\OccurrenceType[] $occurrence
     * @return self
     */
    public function setOccurrence(array $occurrence)
    {
        $this->occurrence = $occurrence;
        return $this;
    }


}

