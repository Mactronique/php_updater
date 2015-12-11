<?php
/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */

namespace JbNahan\PhpUpdate\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class SourceConfig implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('php_sources');

        $rootNode
            ->children()
                ->scalarNode('master')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('archives')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('versions')
                        ->children()
                            ->arrayNode('php56')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php56_nts')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php56_x64')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php56_x64_nts')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php70')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php70_nts')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php70_x64')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()

                            ->arrayNode('php70_x64_nts')
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('version')
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    //->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
