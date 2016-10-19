<?php

namespace InterInvest\AsponeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use II\Bundle\FinancialBundle\Entity\AsponeDeclaration;
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", length=2, nullable=true)
     */
    private $date;

    /**
     * @var AsponeDeclaration
     *
     * @ORM\ManyToOne(targetEntity="II\Bundle\FinancialBundle\Entity\AsponeDeclaration", inversedBy="historiques")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="declaration_id", referencedColumnName="id")
     * })
     */
    private $declaration;

    /**
     * @ORM\OneToMany(targetEntity="DeclarationHistoriqueDetail", mappedBy="declarationHistorique")
     */
    private $detail;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->detail = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DeclarationHistorique
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return DeclarationHistorique
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set iserror
     *
     * @param boolean $iserror
     *
     * @return DeclarationHistorique
     */
    public function setIserror($iserror)
    {
        $this->iserror = $iserror;

        return $this;
    }

    /**
     * Get iserror
     *
     * @return boolean
     */
    public function getIserror()
    {
        return $this->iserror;
    }

    /**
     * Set isfinal
     *
     * @param boolean $isfinal
     *
     * @return DeclarationHistorique
     */
    public function setIsfinal($isfinal)
    {
        $this->isfinal = $isfinal;

        return $this;
    }

    /**
     * Get isfinal
     *
     * @return boolean
     */
    public function getIsfinal()
    {
        return $this->isfinal;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DeclarationHistorique
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set declaration
     *
     * @param \II\Bundle\FinancialBundle\Entity\AsponeDeclaration $declaration
     *
     * @return DeclarationHistorique
     */
    public function setDeclaration(\II\Bundle\FinancialBundle\Entity\AsponeDeclaration $declaration = null)
    {
        $this->declaration = $declaration;

        return $this;
    }

    /**
     * Get declaration
     *
     * @return \II\Bundle\FinancialBundle\Entity\AsponeDeclaration
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    /**
     * Add detail
     *
     * @param \InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail $detail
     *
     * @return DeclarationHistorique
     */
    public function addDetail(\InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail $detail)
    {
        $this->detail[] = $detail;

        return $this;
    }

    /**
     * Remove detail
     *
     * @param \InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail $detail
     */
    public function removeDetail(\InterInvest\AsponeBundle\Entity\DeclarationHistoriqueDetail $detail)
    {
        $this->detail->removeElement($detail);
    }

    /**
     * Get detail
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
