<?php

namespace InterInvest\AsponeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeclarationHistoriqueDetail
 *
 * @ORM\Table(name="aspone_declaration_historique_detail")
 * @ORM\Entity
 */
class DeclarationHistoriqueDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true, options={"default": ""})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true, options={"default": ""})
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="detaillabel", type="text", length=2500, nullable=true, options={"default": ""})
     */
    private $detail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="iserror", type="boolean", nullable=true, options={"default": false})
     */
    private $iserror = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isfinal", type="boolean", nullable=true, options={"default": false})
     */
    private $isfinal = false;

    /**
     * @var string
     *
     * @ORM\Column(name="code_erreur", type="string", length=3, nullable=true, options={"default": ""})
     */
    private $codeErreur;

    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var DeclarationHistorique
     *
     * @ORM\ManyToOne(targetEntity="InterInvest\AsponeBundle\Entity\DeclarationHistorique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="declaration_historique_id", referencedColumnName="id")
     * })
     */
    private $declarationHistorique;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param string $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * @return boolean
     */
    public function getIserror()
    {
        return $this->iserror;
    }

    /**
     * @param boolean $iserror
     */
    public function setIserror($iserror)
    {
        $this->iserror = $iserror;
    }

    /**
     * @return boolean
     */
    public function getIsfinal()
    {
        return $this->isfinal;
    }

    /**
     * @param boolean $isfinal
     */
    public function setIsfinal($isfinal)
    {
        $this->isfinal = $isfinal;
    }

    /**
     * @return int
     */
    public function getCodeErreur()
    {
        return $this->codeErreur;
    }

    /**
     * @param int $codeErreur
     */
    public function setCodeErreur($codeErreur)
    {
        $this->codeErreur = $codeErreur;
    }

    /**
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return DeclarationHistorique
     */
    public function getDeclarationHistorique()
    {
        return $this->declarationHistorique;
    }

    /**
     * @param DeclarationHistorique $declarationHistorique
     */
    public function setDeclarationHistorique($declarationHistorique)
    {
        $this->declarationHistorique = $declarationHistorique;
    }

}