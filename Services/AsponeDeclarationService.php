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
class AsponeDeclarationService
{
    public function __construct(){}

    /**
     * @param DeclarableInterface $declarable
     * @param Declaration         $declaration
     * @param mixed               $formulaires
     *
     * @return Declaration
     * @throws \Exception
     */
    public function createDeclarationsFromDeclarable($declarable, &$declaration, $formulaires)
    {
        if (is_array($formulaires)) {
            $strFormulaires = implode(',', $formulaires);
        } else {
            $strFormulaires = str_replace(' ', '', $formulaires);
        }

        if ($declarable instanceof DeclarableInterface) {
            $declaration->setType($declarable->getType());
            $declaration->setEtat(Declaration::ETAT_NON_FINIE);
            $declaration->setDeclarantSiren($declarable->getRedevableIdentifiant());
            $declaration->setPeriodeStart(date_create_from_format('YmdHis', (method_exists($declarable, 'getPeriode') ? $declarable->getPeriode()[0] : $declarable->getAnnee() . '0101') . '000000'));
            $declaration->setPeriodeEnd(date_create_from_format('YmdHis', (method_exists($declarable, 'getPeriode') ? $declarable->getPeriode()[1] : $declarable->getAnnee() . '1231') . '000000'));
            $declaration->setFormulaires($strFormulaires);
            return $declaration;
        } else {
            throw new \Exception('L\'objet déclarable doit implémenter l\'interface DeclarableInterface');
        }
    }
}