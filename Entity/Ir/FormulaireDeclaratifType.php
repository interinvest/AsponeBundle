<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

/**
 * Class representing FormulaireDeclaratifType
 *
 *
 * XSD Type: FormulaireDeclaratif
 */
class FormulaireDeclaratifType extends FormulaireType
{

    /**
     * @property string $nom
     */
    private $nom = null;

    /**
     * Gets as nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Sets a new nom
     *
     * @param string $nom
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }


}

