<?php

namespace InterInvest\AsponeBundle\Entity;

interface DepositInterface
{
    public function getId();

    public function setIdentifiant($identifiant);
    /** Returns cryptolog identif */
    public function getIdentifiant();

    public function setType($type);
    public function getType();
    public function setNumads($numAds);
    public function getNumads();
    public function setInterchangeId($interchangeId);
    public function getInterchangeId();
}
