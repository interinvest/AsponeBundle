<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

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
     * @property \InterInvest\AsponeBundle\Entity\Ir\EmetteurType $emetteur
     */
    private $emetteur = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\RedacteurType $redacteur
     */
    private $redacteur = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\RedevableType $redevable
     */
    private $redevable = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\PartenaireEdiType $partenaireEdi
     */
    private $partenaireEdi = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\DestinataireType[]
     * $listeDestinataires
     */
    private $listeDestinataires = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\ListeFormulairesType
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
     * @return \InterInvest\AsponeBundle\Entity\Ir\EmetteurType
     */
    public function getEmetteur()
    {
        return $this->emetteur;
    }

    /**
     * Sets a new emetteur
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\EmetteurType $emetteur
     * @return self
     */
    public function setEmetteur(\InterInvest\AsponeBundle\Entity\Ir\EmetteurType $emetteur)
    {
        $this->emetteur = $emetteur;
        return $this;
    }

    /**
     * Gets as redacteur
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\RedacteurType
     */
    public function getRedacteur()
    {
        return $this->redacteur;
    }

    /**
     * Sets a new redacteur
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\RedacteurType $redacteur
     * @return self
     */
    public function setRedacteur(\InterInvest\AsponeBundle\Entity\Ir\RedacteurType $redacteur)
    {
        $this->redacteur = $redacteur;
        return $this;
    }

    /**
     * Gets as redevable
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\RedevableType
     */
    public function getRedevable()
    {
        return $this->redevable;
    }

    /**
     * Sets a new redevable
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\RedevableType $redevable
     * @return self
     */
    public function setRedevable(\InterInvest\AsponeBundle\Entity\Ir\RedevableType $redevable)
    {
        $this->redevable = $redevable;
        return $this;
    }

    /**
     * Gets as partenaireEdi
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\PartenaireEdiType
     */
    public function getPartenaireEdi()
    {
        return $this->partenaireEdi;
    }

    /**
     * Sets a new partenaireEdi
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\PartenaireEdiType $partenaireEdi
     * @return self
     */
    public function setPartenaireEdi(\InterInvest\AsponeBundle\Entity\Ir\PartenaireEdiType $partenaireEdi)
    {
        $this->partenaireEdi = $partenaireEdi;
        return $this;
    }

    /**
     * Adds as destinataire
     *
     * @return self
     * @param \InterInvest\AsponeBundle\Entity\Ir\DestinataireType $destinataire
     */
    public function addToListeDestinataires(\InterInvest\AsponeBundle\Entity\Ir\DestinataireType $destinataire)
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
     * @return \InterInvest\AsponeBundle\Entity\Ir\DestinataireType[]
     */
    public function getListeDestinataires()
    {
        return $this->listeDestinataires;
    }

    /**
     * Sets a new listeDestinataires
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\DestinataireType[]
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
     * @return \InterInvest\AsponeBundle\Entity\Ir\ListeFormulairesType
     */
    public function getListeFormulaires()
    {
        return $this->listeFormulaires;
    }

    /**
     * Sets a new listeFormulaires
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\ListeFormulairesType
     * $listeFormulaires
     * @return self
     */
    public function setListeFormulaires(\InterInvest\AsponeBundle\Entity\Ir\ListeFormulairesType $listeFormulaires)
    {
        $this->listeFormulaires = $listeFormulaires;
        return $this;
    }


}

