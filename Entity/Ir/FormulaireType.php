<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

/**
 * Class representing FormulaireType
 *
 *
 * XSD Type: Formulaire
 */
class FormulaireType
{

    /**
     * @property string $millesime
     */
    private $millesime = null;

    /**
     * @property string $repetition
     */
    private $repetition = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\ZoneType[] $zone
     */
    private $zone = array(
        
    );

    /**
     * Gets as millesime
     *
     * @return string
     */
    public function getMillesime()
    {
        return $this->millesime;
    }

    /**
     * Sets a new millesime
     *
     * @param string $millesime
     * @return self
     */
    public function setMillesime($millesime)
    {
        $this->millesime = $millesime;
        return $this;
    }

    /**
     * Gets as repetition
     *
     * @return string
     */
    public function getRepetition()
    {
        return $this->repetition;
    }

    /**
     * Sets a new repetition
     *
     * @param string $repetition
     * @return self
     */
    public function setRepetition($repetition)
    {
        $this->repetition = $repetition;
        return $this;
    }

    /**
     * Adds as zone
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Ir\ZoneType $zone
     */
    public function addToZone(\InterInvest\AsponeBundle\Entity\Ir\ZoneType $zone)
    {
        $this->zone[] = $zone;
        return $this;
    }

    /**
     * isset zone
     *
     * @param scalar $index
     * @return boolean
     */
    public function issetZone($index)
    {
        return isset($this->zone[$index]);
    }

    /**
     * unset zone
     *
     * @param scalar $index
     * @return void
     */
    public function unsetZone($index)
    {
        unset($this->zone[$index]);
    }

    /**
     * Gets as zone
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\ZoneType[]
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Sets a new zone
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\ZoneType[] $zone
     * @return self
     */
    public function setZone(array $zone)
    {
        $this->zone = $zone;
        return $this;
    }


}

