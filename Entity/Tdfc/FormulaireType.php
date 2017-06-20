<?php

namespace InterInvest\AsponeBundle\Entity\Tdfc;

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
     * @property \InterInvest\AsponeBundle\Entity\Tdfc\ZoneType[] $zone
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
     * Adds as zone
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Tdfc\ZoneType $zone
     */
    public function addToZone(\InterInvest\AsponeBundle\Entity\Tdfc\ZoneType $zone)
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
     * @return \InterInvest\AsponeBundle\Entity\Tdfc\ZoneType[]
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Sets a new zone
     *
     * @param \InterInvest\AsponeBundle\Entity\Tdfc\ZoneType[] $zone
     * @return self
     */
    public function setZone(array $zone)
    {
        $this->zone = $zone;
        return $this;
    }


}

