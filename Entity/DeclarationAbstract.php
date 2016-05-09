<?php

namespace InterInvest\AsponeBundle\Entity;

abstract class DeclarationAbstract
{
    protected $declarable;
    protected $identifiant;
    protected $typeDeclaration;
    protected $deposit;
    protected $etat;
    protected $historiques;
    protected $xml;

    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
        return $this;
    }
    /** Returns cryptolog identif */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    public function setTypeDeclaration($typeDeclaration){
        $this->typeDeclaration = $typeDeclaration;
        return $this;
    }
    public function getTypeDeclaration()
    {
        return $this->typeDeclaration;
    }

    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
        return $this;
    }
    public function getDeposit()
    {
        return $this->deposit;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }
    public function getEtat()
    {
        return $this->etat;
    }

    public function setHistoriques($historiques)
    {
        $this->historiques = $historiques;
        return $this;
    }
    public function getHistoriques()
    {
        return $this->historiques;
    }

    /**
     * Initialise l'objet déclarable relié à la déclaration
     *
     * @param $declarable
     *
     * @return $this
     */
    public function setDeclarable($declarable)
    {
        $this->declarable = $declarable;
        return $this;
    }

    public function getDeclarable()
    {
        return $this->declarable;
    }

    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Sauvegarde de l'objet déclaration AspOne relié au déclarable
     *
     * @return mixed
     */
    abstract function save();
}