<?php

/** Makes a Declaration object from a Declarable object and use it */

namespace InterInvest\AsponeBundle\Services;

use InterInvest\AsponeBundle\Entity;
use InterInvest\AsponeBundle\Entity\DeclarableInterface;
use InterInvest\AsponeBundle\Entity\DeclarationAbstract;
use InterInvest\AsponeBundle\Entity\DeclarationRbt;

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
        $declaration->save();

        $declarations = array($declaration);

        if ($declarable->getType() == 'TVA' && $declarable->get3310CA3JB()) {
            $declarationRbt = new DeclarationRbt();
            $declarationRbt->setDeclarable($declarable);
            $declarationRbt->save();

            array_push($declarations, $declarationRbt);
        }

        return $declarations;
    }
}