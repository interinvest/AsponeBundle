<?php

namespace InterInvest\AsponeBundle\Entity\Requete;

/**
 * Class representing ListeFormulairesType
 *
 *
 * XSD Type: ListeFormulaires
 */
class ListeFormulairesType
{

    /**
     * @property \InterInvest\AsponeBundle\Entity\Requete\FormulaireType $identif
     */
    private $identif = null;

    /**
     * Gets as identif
     *
     * @return \InterInvest\AsponeBundle\Entity\Requete\FormulaireType
     */
    public function getIdentif()
    {
        return $this->identif;
    }

    /**
     * Sets a new identif
     *
     * @param \InterInvest\AsponeBundle\Entity\Requete\FormulaireType $identif
     * @return self
     */
    public function setIdentif(\InterInvest\AsponeBundle\Entity\Requete\FormulaireType $identif)
    {
        $this->identif = $identif;
        return $this;
    }


}

