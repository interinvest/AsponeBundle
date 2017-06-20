<?php

namespace InterInvest\AsponeBundle\Entity\Tva;

/**
 * Class representing DeclarationType
 *
 *
 * XSD Type: Declaration
 */
class DeclarationType
{

    /**
     * @property string $type
     */
    private $type = null;

    /**
     * @property string $reference
     */
    private $reference = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\EmetteurType $emetteur
     */
    private $emetteur = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\RedacteurType $redacteur
     */
    private $redacteur = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\RedevableType $redevable
     */
    private $redevable = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\PartenaireEdiType $partenaireEdi
     */
    private $partenaireEdi = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\DestinataireType[]
     * $listeDestinataires
     */
    private $listeDestinataires = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Tva\ListeFormulairesType
     * $listeFormulaires
     */
    private $listeFormulaires = null;

    /**
     * Gets as type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets a new type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Gets as reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Sets a new reference
     *
     * @param string $reference
     * @return self
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Gets as emetteur
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\EmetteurType
     */
    public function getEmetteur()
    {
        return $this->emetteur;
    }

    /**
     * Sets a new emetteur
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\EmetteurType $emetteur
     * @return self
     */
    public function setEmetteur(\InterInvest\AsponeBundle\Entity\Tva\EmetteurType $emetteur)
    {
        $this->emetteur = $emetteur;
        return $this;
    }

    /**
     * Gets as redacteur
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\RedacteurType
     */
    public function getRedacteur()
    {
        return $this->redacteur;
    }

    /**
     * Sets a new redacteur
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\RedacteurType $redacteur
     * @return self
     */
    public function setRedacteur(\InterInvest\AsponeBundle\Entity\Tva\RedacteurType $redacteur)
    {
        $this->redacteur = $redacteur;
        return $this;
    }

    /**
     * Gets as redevable
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\RedevableType
     */
    public function getRedevable()
    {
        return $this->redevable;
    }

    /**
     * Sets a new redevable
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\RedevableType $redevable
     * @return self
     */
    public function setRedevable(\InterInvest\AsponeBundle\Entity\Tva\RedevableType $redevable)
    {
        $this->redevable = $redevable;
        return $this;
    }

    /**
     * Gets as partenaireEdi
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\PartenaireEdiType
     */
    public function getPartenaireEdi()
    {
        return $this->partenaireEdi;
    }

    /**
     * Sets a new partenaireEdi
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\PartenaireEdiType $partenaireEdi
     * @return self
     */
    public function setPartenaireEdi(\InterInvest\AsponeBundle\Entity\Tva\PartenaireEdiType $partenaireEdi)
    {
        $this->partenaireEdi = $partenaireEdi;
        return $this;
    }

    /**
     * Adds as destinataire
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Tva\DestinataireType $destinataire
     */
    public function addToListeDestinataires(\InterInvest\AsponeBundle\Entity\Tva\DestinataireType $destinataire)
    {
        $this->listeDestinataires[] = $destinataire;
        return $this;
    }

    /**
     * isset listeDestinataires
     *
     * @param scalar $index
     * @return boolean
     */
    public function issetListeDestinataires($index)
    {
        return isset($this->listeDestinataires[$index]);
    }

    /**
     * unset listeDestinataires
     *
     * @param scalar $index
     * @return void
     */
    public function unsetListeDestinataires($index)
    {
        unset($this->listeDestinataires[$index]);
    }

    /**
     * Gets as listeDestinataires
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\DestinataireType[]
     */
    public function getListeDestinataires()
    {
        return $this->listeDestinataires;
    }

    /**
     * Sets a new listeDestinataires
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\DestinataireType[]
     * $listeDestinataires
     * @return self
     */
    public function setListeDestinataires(array $listeDestinataires)
    {
        $this->listeDestinataires = $listeDestinataires;
        return $this;
    }

    /**
     * Gets as listeFormulaires
     *
     * @return \InterInvest\AsponeBundle\Entity\Tva\ListeFormulairesType
     */
    public function getListeFormulaires()
    {
        return $this->listeFormulaires;
    }

    /**
     * Sets a new listeFormulaires
     *
     * @param \InterInvest\AsponeBundle\Entity\Tva\ListeFormulairesType
     * $listeFormulaires
     * @return self
     */
    public function setListeFormulaires(\InterInvest\AsponeBundle\Entity\Tva\ListeFormulairesType $listeFormulaires)
    {
        $this->listeFormulaires = $listeFormulaires;
        return $this;
    }


}

