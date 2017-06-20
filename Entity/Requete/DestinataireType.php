<?php

namespace InterInvest\AsponeBundle\Entity\Requete;

/**
 * Class representing DestinataireType
 *
 *
 * XSD Type: Destinataire
 */
class DestinataireType
{

    /**
     * @property string $designation
     */
    private $designation = null;

    /**
     * Gets as designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Sets a new designation
     *
     * @param string $designation
     * @return self
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
        return $this;
    }


}

