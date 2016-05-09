<?php

namespace InterInvest\AsponeBundle\Entity;

class DeclarationTva extends DeclarationAbstract
{

    protected $typeDeclaration = 'TVA';

    /**
     * Sauvegarde de l'objet déclaration AspOne relié au déclarable
     *
     * @return mixed
     */
    public function save()
    {

    }
}