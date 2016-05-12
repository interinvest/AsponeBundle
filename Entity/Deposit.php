<?php

namespace InterInvest\AsponeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AsponeDeposit
 *
 * @ORM\Table(name="aspone_deposit")
 * @ORM\Entity
 */
class Deposit
{

    const ETAT_NON_FINI = 0;
    const ETAT_OK = 1;
    const ETAT_ERREUR = 2;

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
     * @ORM\Column(name="type", type="string", length=5, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="identifiant", type="string", length=100, nullable=true)
     */
    private $identifiant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $dateEnvoi;

    /**
     * @var integer
     *
     * @ORM\Column(name="retour_immediat", type="integer", length=2, nullable=true)
     */
    private $retourImmediat;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="integer", length=2, nullable=true)
     */
    private $etat;

    /**
     * @var integer
     *
     * @ORM\Column(name="numads", type="integer", length=10, nullable=true)
     */
    private $numads;

    /**
     * @var integer
     *
     * @ORM\Column(name="interchangeid", type="integer", length=10, nullable=true)
     */
    private $interchangeid;


    public function __construct()
    {
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * @param string $identifiant
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * @param \DateTime $dateEnvoi
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;
    }

    /**
     * @return int
     */
    public function getRetourImmediat()
    {
        return $this->retourImmediat;
    }

    /**
     * @param int $retourImmediat
     */
    public function setRetourImmediat($retourImmediat)
    {
        $this->retourImmediat = $retourImmediat;
    }

    /**
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param int $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return int
     */
    public function getNumads()
    {
        return $this->numads;
    }

    /**
     * @param int $numads
     */
    public function setNumads($numads)
    {
        $this->numads = $numads;
    }

    /**
     * @return int
     */
    public function getInterchangeid()
    {
        return $this->interchangeid;
    }

    /**
     * @param int $interchangeid
     */
    public function setInterchangeid($interchangeid)
    {
        $this->interchangeid = $interchangeid;
    }

    /**
     * @param array $declarations
     */
    public function setDeclarations(array $declarations)
    {
        /** @var Declaration $declaration */
        foreach ($declarations as $declaration)
        {
            $declaration->setDepositId($this->getId());
        }
    }
}
