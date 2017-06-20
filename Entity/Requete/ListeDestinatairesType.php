<?php

namespace InterInvest\AsponeBundle\Entity\Requete;

/**
 * Class representing ListeDestinatairesType
 *
 *
 * XSD Type: ListeDestinataires
 */
class ListeDestinatairesType
{

    /**
     * @property \InterInvest\AsponeBundle\Entity\Requete\DestinataireType
     * $destinataire
     */
    private $destinataire = null;

    /**
     * Gets as destinataire
     *
     * @return \InterInvest\AsponeBundle\Entity\Requete\DestinataireType
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * Sets a new destinataire
     *
     * @param \InterInvest\AsponeBundle\Entity\Requete\DestinataireType $destinataire
     * @return self
     */
    public function setDestinataire(\InterInvest\AsponeBundle\Entity\Requete\DestinataireType $destinataire)
    {
        $this->destinataire = $destinataire;
        return $this;
    }


}

