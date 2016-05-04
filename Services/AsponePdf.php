<?php
/**
 * Gère les pdf liés aux déclarations AspOne
 */
namespace InterInvest\AsponeBundle\Services;


class AsponePdf
{
    private $em;
    private $container;


    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
}