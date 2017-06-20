<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

/**
 * Class representing RedevableType
 *
 *
 * XSD Type: Redevable
 */
class RedevableType
{

    /**
     * @property string $identifiant
     */
    private $identifiant = null;

    /**
     * @property string $designation
     */
    private $designation = null;

    /**
     * @property string $referenceDossier
     */
    private $referenceDossier = null;

    /**
     * @property string $identifiantRedevable2
     */
    private $identifiantRedevable2 = null;

    /**
     * @property string $sirenActivitePrincipale
     */
    private $sirenActivitePrincipale = null;

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

    /**
     * Gets as identifiantRedevable2
     *
     * @return string
     */
    public function getIdentifiantRedevable2()
    {
        return $this->identifiantRedevable2;
    }

    /**
     * Sets a new identifiantRedevable2
     *
     * @param string $identifiantRedevable2
     * @return self
     */
    public function setIdentifiantRedevable2($identifiantRedevable2)
    {
        $this->identifiantRedevable2 = $identifiantRedevable2;
        return $this;
    }

    /**
     * Gets as sirenActivitePrincipale
     *
     * @return string
     */
    public function getSirenActivitePrincipale()
    {
        return $this->sirenActivitePrincipale;
    }

    /**
     * Sets a new sirenActivitePrincipale
     *
     * @param string $sirenActivitePrincipale
     * @return self
     */
    public function setSirenActivitePrincipale($sirenActivitePrincipale)
    {
        $this->sirenActivitePrincipale = $sirenActivitePrincipale;
        return $this;
    }


}

