<?php

namespace InterInvest\AsponeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AsponeDeclaration
 */
abstract class Declaration
{
    const TYPE_RBT  = 'RBT';
    const TYPE_IDT  = 'IDT';
    const TYPE_IDF  = 'IDF';

    const ETAT_NON_FINIE = 0;
    const ETAT_OK = 1;
    const ETAT_ERREUR = 2;

    public static $correspondancesTypes = array(
        'TVA'  => array('IDT', 'RBT'),
        'TDFC' => array('CVA', 'CRM', 'IAT', 'IDF', 'ILF', 'LIS', 'LOY'),
    );

//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="id", type="integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="IDENTITY")
//     */
//    private $id;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="type", type="string", length=5, nullable=true)
//     */
//    private $type;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="identifiant", type="string", length=100, nullable=true)
//     */
//    private $identifiant;
//
//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="etat", type="integer", length=2, nullable=true)
//     */
//    private $etat;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="declarant_siren", type="string", length=20, nullable=true)
//     */
//    private $declarantSiren;
//
//    /**
//     * @var datetime
//     *
//     * @ORM\Column(name="periode_start", type="datetime", nullable=true)
//     */
//    private $periodeStart;
//
//    /**
//     * @var datetime
//     *
//     * @ORM\Column(name="periode_end", type="datetime", nullable=true)
//     */
//    private $periodeEnd;
//
//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="deposit_id", type="integer", length=10, nullable=true)
//     */
//    private $depositId;
//
//    /**
//     * @var Deposit
//     *
//     * @ORM\ManyToOne(targetEntity="InterInvest\AsponeBundle\Entity\Deposit")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
//     * })
//     */
//    private $deposit;
//
//    /**
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    /**
//     * @param int $id
//     */
//    public function setId($id)
//    {
//        $this->id = $id;
//    }
//
//    /**
//     * @return string
//     */
//    public function getType()
//    {
//        return $this->type;
//    }
//
//    /**
//     * @param string $type
//     */
//    public function setType($type)
//    {
//        $this->type = $type;
//    }
//
//    /**
//     * @return string
//     */
//    public function getIdentifiant()
//    {
//        return $this->identifiant;
//    }
//
//    /**
//     * @param string $identifiant
//     */
//    public function setIdentifiant($identifiant)
//    {
//        $this->identifiant = $identifiant;
//    }
//
//    /**
//     * @return int
//     */
//    public function getEtat()
//    {
//        return $this->etat;
//    }
//
//    /**
//     * @param int $etat
//     */
//    public function setEtat($etat)
//    {
//        $this->etat = $etat;
//    }
//
//    /**
//     * @return int
//     */
//    public function getDepositId()
//    {
//        return $this->depositId;
//    }
//
//    /**
//     * @param int $depositId
//     */
//    public function setDepositId($depositId)
//    {
//        $this->depositId = $depositId;
//    }
//
//    /**
//     * @return string
//     */
//    public function getDeclarantSiren()
//    {
//        return $this->declarantSiren;
//    }
//
//    /**
//     * @param string $declarantSiren
//     */
//    public function setDeclarantSiren($declarantSiren)
//    {
//        $this->declarantSiren = $declarantSiren;
//    }
//
//    /**
//     * @return datetime
//     */
//    public function getPeriodeStart()
//    {
//        return $this->periodeStart;
//    }
//
//    /**
//     * @param datetime $periodeStart
//     */
//    public function setPeriodeStart($periodeStart)
//    {
//        $this->periodeStart = $periodeStart;
//    }
//
//    /**
//     * @return datetime
//     */
//    public function getPeriodeEnd()
//    {
//        return $this->periodeEnd;
//    }
//
//    /**
//     * @param datetime $periodeEnd
//     */
//    public function setPeriodeEnd($periodeEnd)
//    {
//        $this->periodeEnd = $periodeEnd;
//    }
//
//    /**
//     * @return Deposit
//     */
//    public function getDeposit()
//    {
//        return $this->deposit;
//    }
//
//    /**
//     * @param Deposit $deposit
//     */
//    public function setDeposit($deposit)
//    {
//        $this->deposit = $deposit;
//    }
//
//    /**
//     * @param string $xml
//     * @param string $path
//     */
//    public function archiveXml($xml, $path)
//    {
//        $xml = new \SimpleXMLElement($xml);
//
//        if (!file_exists($path) || !is_dir($path)) {
//            mkdir($path);
//        }
//
//        $path .=  '/' . $this->getType();
//
//        if (!file_exists($path) || !is_dir($path)) {
//            mkdir($path);
//        }
//
//        $xml->saveXML($path . '/' . $this->getId() . '.xml');
//    }
//
//    /**
//     * @return string
//     */
//    public function getXmlPath()
//    {
//        return '/' . $this->getType() . '/' . $this->getId() . '.xml';
//    }
//


    /**
     * Doit renvoyer un tableau avec le nom du service pour la création de l'objet déclarable et l'entité utilisée
     * @return mixed
     */
    abstract function getServiceDeclarable();

}