<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

/**
 * Class representing ListeFormulairesType
 *
 *
 * XSD Type: ListeFormulaires
 */
class ListeFormulairesType
{

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif0
     */
    private $identif0 = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\FormulaireType[] $identif1
     */
    private $identif1 = array(
        
    );

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif2
     */
    private $identif2 = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif3
     */
    private $identif3 = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\FormulaireDeclaratifType[]
     * $formulaire
     */
    private $formulaire = array(
        
    );

    /**
     * Gets as identif0
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\FormulaireType
     */
    public function getIdentif0()
    {
        return $this->identif0;
    }

    /**
     * Sets a new identif0
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif0
     * @return self
     */
    public function setIdentif0(\InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif0)
    {
        $this->identif0 = $identif0;
        return $this;
    }

    /**
     * Adds as identif1
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif1
     */
    public function addToIdentif1(\InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif1)
    {
        $this->identif1[] = $identif1;
        return $this;
    }

    /**
     * isset identif1
     *
     * @param scalar $index
     * @return boolean
     */
    public function issetIdentif1($index)
    {
        return isset($this->identif1[$index]);
    }

    /**
     * unset identif1
     *
     * @param scalar $index
     * @return void
     */
    public function unsetIdentif1($index)
    {
        unset($this->identif1[$index]);
    }

    /**
     * Gets as identif1
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\FormulaireType[]
     */
    public function getIdentif1()
    {
        return $this->identif1;
    }

    /**
     * Sets a new identif1
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireType[] $identif1
     * @return self
     */
    public function setIdentif1(array $identif1)
    {
        $this->identif1 = $identif1;
        return $this;
    }

    /**
     * Gets as identif2
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\FormulaireType
     */
    public function getIdentif2()
    {
        return $this->identif2;
    }

    /**
     * Sets a new identif2
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif2
     * @return self
     */
    public function setIdentif2(\InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif2)
    {
        $this->identif2 = $identif2;
        return $this;
    }

    /**
     * Gets as identif3
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\FormulaireType
     */
    public function getIdentif3()
    {
        return $this->identif3;
    }

    /**
     * Sets a new identif3
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif3
     * @return self
     */
    public function setIdentif3(\InterInvest\AsponeBundle\Entity\Ir\FormulaireType $identif3)
    {
        $this->identif3 = $identif3;
        return $this;
    }

    /**
     * Adds as formulaire
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireDeclaratifType $formulaire
     */
    public function addToFormulaire(\InterInvest\AsponeBundle\Entity\Ir\FormulaireDeclaratifType $formulaire)
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
     * @return \InterInvest\AsponeBundle\Entity\Ir\FormulaireDeclaratifType[]
     */
    public function getFormulaire()
    {
        return $this->formulaire;
    }

    /**
     * Sets a new formulaire
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\FormulaireDeclaratifType[]
     * $formulaire
     * @return self
     */
    public function setFormulaire(array $formulaire)
    {
        $this->formulaire = $formulaire;
        return $this;
    }


}

