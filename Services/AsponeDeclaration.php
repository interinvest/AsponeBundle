<?php

/** Makes a Declaration object from a Declarable object and use it */

namespace InterInvest\AsponeBundle\Services;

use InterInvest\AsponeBundle\Entity\Declaration;
use InterInvest\AsponeBundle\Entity\DeclarableInterface;

/**
 * Class AsponeDeclaration
 *
 * Service à implémenter en interne pour créer vos propres entités issues de Declaration et enregistrer vos objets
 *
 * @package InterInvest\AsponeBundle\Services
 */
class AsponeDeclaration
{
    public function __construct(){}

    /**
     * @param DeclarableInterface $declarable
     *
     * @return Declaration
     * @throws \Exception
     */
    public function createDeclarationsFromDeclarable($declarable)
    {
        if ($declarable instanceof DeclarableInterface) {
            $declaration = new Declaration();
            $declaration->setType($declarable->getType());
            $declaration->setEtat(Declaration::ETAT_NON_FINIE);
            $declaration->setDeclarantSiren($declarable->getRedevableIdentifiant());
            $declaration->setPeriodeStart(date_create_from_format('YmdHis', $declarable->getTIdentifCA() . '000000'));
            $declaration->setPeriodeEnd(date_create_from_format('YmdHis', $declarable->getTIdentifCB() . '000000'));

            return $declaration;
        } else {
            throw new \Exception('L\'objet déclarable doit implémenter l\'interface DeclarableInterface');
        }
    }
}