<?php

namespace InterInvest\AsponeBundle\Entity\Tva;

/**
 * Class representing DestinataireType
 *
 *
 * XSD Type: Destinataire
 */
class DestinataireType
{

    /**
     * @property string $designation
     */
    private $designation = null;

    /**
     * @property string $identifiant
     */
    private $identifiant = null;

    /**
     * @property string $designationSuite1
     */
    private $designationSuite1 = null;

    /**
     * @property string $designationSuite2
     */
    private $designationSuite2 = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\Adresse $adresse
     */
    private $adresse = null;

    /**
     * @property string $referenceDossier
     */
    private $referenceDossier = null;

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
     * Gets as adresse
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Sets a new adresse
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\Adresse $adresse
     * @return self
     */
    public function setAdresse(\InterInvest\AsponeBundle\Entity\Tva\Adresse $adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Gets as referenceDossier
     *
     * @return string
     */
    public function getReferenceDossier()
    {
        return $this->referenceDossier;
    }

    /**
     * Sets a new referenceDossier
     *
     * @param string $referenceDossier
     * @return self
     */
    public function setReferenceDossier($referenceDossier)
    {
        $this->referenceDossier = $referenceDossier;
        return $this;
    }


}

