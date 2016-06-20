<?php

namespace InterInvest\AsponeBundle\Entity;

interface DeclarableInterface
{
    /**
     * @param $declaration
     * @param $formulaires List of forms to use
     *
     * @return mixed
     */
    public function init($declaration, $formulaires);

    public function getId();

    /**
     * Return the configuration array of the object.
     * Must be like this : array(form=>array(zone=>array(nodename=>value))
     * Ex : array(2031 => array('CW' => array('Valeur' => 123)))
     * Ex : array('3310CA3' => array('BA' => array('TextLibre1' => 'Lorem Ipsum', 'TextLibre2' => 'Dolor')))
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * @return string 'IDT', 'RBT', 'IDF', ...
     */
    public function getType();

    public function getInfent();

    public function getAnnee();

    public function getDestinataires();

    /**
     * Return array of designation :
     * redevable = array('Siret' => '412346', 'Designation' => 'Redevable', 'AdresseNumero' => 5, ....)
     * @return array
     */
    public function getRedacteur();
    public function getRedevable();
    public function getIdentif();

    /**
     * Return array : faitA, faitLe, adresse, signature
     *
     * @return array
     */
    public function getInformationsSignature();
}
