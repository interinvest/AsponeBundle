<?php

namespace InterInvest\AsponeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail;

/**
 * DeclarationHistorique
 *
 * @ORM\Table(name="aspone_declaration_historique")
 * @ORM\Entity
 */
class DeclarationHistorique
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
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime", length=2, nullable=true)
     */
    private $date;


    /**
     * @var integer
     *
     * @ORM\Column(name="declaration_id", type="integer", nullable=true)
     */
    private $declarationId;

    /**
     * @ORM\OneToMany(targetEntity="DeclarationHistoriqueDetail", mappedBy="declarationHistorique")
     */
    private $detail;

    /**
     * @return mixed
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param mixed $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }


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
     * @return int
     */
    public function getDeclarationId()
    {
        return $this->declarationId;
    }

    /**
     * @param int $declarationId
     */
    public function setDeclarationId($declarationId)
    {
        $this->declarationId = $declarationId;
    }
}