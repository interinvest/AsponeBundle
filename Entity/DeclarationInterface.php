<?php
namespace InterInvest\AsponeBundle\Entity;

interface DeclarationInterface
{
    public function getId();

    public function setIdentifiant($identifiant);
    /** Returns cryptolog identif */
    public function getIdentifiant();

    public function setType($type);
    public function getType();

    public function setDeposit($deposit);
    public function getDeposit();

    public function setEtat($etat);
    public function getEtat();

    public function setDeclarable($declarable);
    public function getDeclarable();

    public function getXml();
}