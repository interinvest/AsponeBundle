<?php

namespace InterInvest\AsponeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aspone');

        $rootNode
            ->children()
                ->scalarNode('username')->defaultValue('ASPONE')->end()
                ->scalarNode('context')->defaultValue('http://aspone.fr/mb/webservices')->end()
                ->scalarNode('password')->isRequired()->end()
                ->scalarNode('contextLogin')->isRequired()->end()
                ->scalarNode('contextPassword')->isRequired()->end()
                ->enumNode('archive')
                    ->isRequired()
                    ->values(array('yes', 'no'))
                ->end()
                ->scalarNode('xmlPath')->isRequired()->end()
                ->arrayNode('wsdl')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('teledeclarations')->defaultValue('https://services-teleprocedures.aspone.fr/ws/deposit?wsdl')->end()
                            ->scalarNode('monitoring')->defaultValue('https://services-teleprocedures.aspone.fr/ws/monitoring?wsdl')->end()
                        ->end()
                    ->end()
                ->arrayNode('location')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('teledeclarations')->defaultValue('https://services-teleprocedures.aspone.fr/ws/deposit')->end()
                            ->scalarNode('monitoring')->defaultValue('https://services-teleprocedures.aspone.fr/ws/monitoring')->end()
                        ->end()
                    ->end()
                ->arrayNode('serviceVersion')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('1')->defaultValue('http://www.cegedim.com/aspone/mb/webservices')->end()
                            ->scalarNode('0')->defaultValue('http://aspone.fr/mb/webservices')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}