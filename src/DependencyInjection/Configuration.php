<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection;

    use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;

    final class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder(): TreeBuilder
        {
            $treeBuilder = new TreeBuilder('neox_dash_board');

            /** @var ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->getRootNode();

            $rootNode
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('widget')
                            ->useAttributeAsKey('name')     // Use dynamic keys
                            ->arrayPrototype()                    // Allow subnodes for each entry
                                ->children()
                                    ->scalarNode('type')->isRequired()->defaultValue('Widget')->end()
                                    ->booleanNode('enabled')->defaultFalse()->end()
                                    ->arrayNode('options')        // Define 'options' as an array
                                        ->scalarPrototype()->end()      // Each option can be a scalar value
                                        ->defaultValue([])              // Default to an empty array
                                    ->end()
                                ->end()
                            ->end()
                            // Set multiple widgets as default
                            ->defaultValue([
                                     'favorite' => [
                                         'type'     => 'Tools',
                                         'enabled'  => true,
                                         "options"  => []
                                     ],
                                     'search' => [
                                         'type'     => 'Tools',
                                         'enabled'  => true,
                                         "options"  => []
                                     ],
                                ])
                    ->end()
                ->end()
            ;

            return $treeBuilder;
        }
    }
