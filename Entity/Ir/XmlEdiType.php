<?php

namespace InterInvest\AsponeBundle\Entity\Ir;

/**
 * Class representing XmlEdiType
 *
 *
 * XSD Type: XmlEdi
 */
class XmlEdiType
{

    /**
     * @property string $test
     */
    private $test = null;

    /**
     * @property \InterInvest\AsponeBundle\Entity\Ir\GroupeFonctionnelType
     * $groupeFonctionnel
     */
    private $groupeFonctionnel = null;

    /**
     * Gets as test
     *
     * @return string
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Sets a new test
     *
     * @param string $test
     * @return self
     */
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * Gets as groupeFonctionnel
     *
     * @return \InterInvest\AsponeBundle\Entity\Ir\GroupeFonctionnelType
     */
    public function getGroupeFonctionnel()
    {
        return $this->groupeFonctionnel;
    }

    /**
     * Sets a new groupeFonctionnel
     *
     * @param \InterInvest\AsponeBundle\Entity\Ir\GroupeFonctionnelType
     * $groupeFonctionnel
     * @return self
     */
    public function setGroupeFonctionnel(\InterInvest\AsponeBundle\Entity\Ir\GroupeFonctionnelType $groupeFonctionnel)
    {
        $this->groupeFonctionnel = $groupeFonctionnel;
        return $this;
    }


}

