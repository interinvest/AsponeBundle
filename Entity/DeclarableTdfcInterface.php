<?php

namespace InterInvest\AsponeBundle\Entity;

interface DeclarableTdfcInterface extends DeclarableInterface
{
    /**
     * @return "CRM" | "CVA" | "IAT" | "IDF" | "ILF" | "LOY" | "IPT"
     */
    public function getTypeDeclaration();

    public function getExercice();

    public function getIsLiquidation();

}