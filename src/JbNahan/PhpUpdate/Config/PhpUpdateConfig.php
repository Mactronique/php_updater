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

class PhpUpdateConfig implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('php_update');

        $rootNode
            ->children()
                ->scalarNode('target')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('tmp_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('php_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('backup_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('php_branch')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue('php56')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
