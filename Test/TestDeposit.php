<?php

namespace InterInvest\AsponeBundle\Test;

use InterInvest\AsponeBundle\Entity\DepositInterface;

class TestDeposit
{

    private $id = 2548;
    private $identifiant = 'EF790A1D-5D8B-4BC1-8D8D-D31EF765B4CD';
    private $type = 'TVA';
    private $numAds = '4569052';
    private $interchangeId = '4585660';

    public function getId()
    {
        return $this->id;
    }

    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
    }

    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public  function setNumAds($numAds)
    {
        $this->numAds = $numAds;
    }

    public function getNumads()
    {
        return $this->numAds;
    }

    public function setInterchangeId($interchangeId)
    {
        $this->interchangeId = $interchangeId;
    }

    public function getInterchangeId()
    {
        return $this->interchangeId;
    }
}
