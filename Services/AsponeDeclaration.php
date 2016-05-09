<?php

/** Makes a Declaration object from a Declarable object and use it */

namespace InterInvest\AsponeBundle\Services;

use InterInvest\AsponeBundle\Entity;
use InterInvest\AsponeBundle\Entity\DeclarableInterface;
use InterInvest\AsponeBundle\Entity\DeclarationAbstract;

class AsponeDeclaration
{
    public function __construct(){}

    public function createDeclarationsFromDeclarable(DeclarableInterface $declarable)
    {
        $objetDeclaration = 'Declaration' . $declarable->getType();

        /** @var DeclarationAbstract $declaration */
        $declaration = new $objetDeclaration();

        $declaration->setEtat(0);
        $declaration->setDeclarable($declarable);

        return $declaration;
    }
}