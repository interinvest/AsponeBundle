<?php

namespace InterInvest\AsponeBundle\Entity\Paiement;

/**
 * Class representing ListeFormulairesType
 *
 *
 * XSD Type: ListeFormulaires
 */
class ListeFormulairesType
{

    /**
     * @property \InterInvest\AsponeBundle\Entity\Paiement\FormulaireType $identif
     */
    private $identif = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Paiement\FormulaireDeclaratifType[]
     * $formulaire
     */
    private $formulaire = array(
        
    );

    /**
     * Gets as identif
     *
     * @return \InterInvest\AsponeBundle\Entity\Paiement\FormulaireType
     */
    public function getIdentif()
    {
        return $this->identif;
    }

    /**
     * Sets a new identif
     *
     * @param \InterInvest\AsponeBundle\Entity\Paiement\FormulaireType $identif
     * @return self
     */
    public function setIdentif(\InterInvest\AsponeBundle\Entity\Paiement\FormulaireType $identif)
    {
        $this->identif = $identif;
        return $this;
    }

    /**
     * Adds as formulaire
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Paiement\FormulaireDeclaratifType
     * $formulaire
     */
    public function addToFormulaire(\InterInvest\AsponeBundle\Entity\Paiement\FormulaireDeclaratifType $formulaire)
    {
        $this->formulaire[] = $formulaire;
        return $this;
    }

    /**
     * isset formulaire
     *
     * @param scalar $index
     * @return boolean
     */
    public function issetFormulaire($index)
    {
        return isset($this->formulaire[$index]);
    }

    /**
     * unset formulaire
     *
     * @param scalar $index
     * @return void
     */
    public function unsetFormulaire($index)
    {
        unset($this->formulaire[$index]);
    }

    /**
     * Gets as formulaire
     *
     * @return \InterInvest\AsponeBundle\Entity\Paiement\FormulaireDeclaratifType[]
     */
    public function getFormulaire()
    {
        return $this->formulaire;
    }

    /**
     * Sets a new formulaire
     *
     * @param \InterInvest\AsponeBundle\Entity\Paiement\FormulaireDeclaratifType[]
     * $formulaire
     * @return self
     */
    public function setFormulaire(array $formulaire)
    {
        $this->formulaire = $formulaire;
        return $this;
    }


}

