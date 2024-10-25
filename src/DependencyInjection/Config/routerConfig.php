<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config;

    /*    
        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__ . '/../../assets/dist/' => "@xorgxx/neox-dashboard-bundle",
                ],
            ],
        ]);
    */

    #controllers_neox_dashboard:
#    resource:
#        path: "../vendor/xorgxx/neox-dashboard-bundle/src/Controller/"
#        namespace: NeoxDashBoard\NeoxDashBoardBundle\Controller
#    #        prefix: "/secure"
#    #        trailing_slash_on_root: true
#    type: attribute

    class routerConfig
    {
        public static function getConfig(): array
        {
            return [
                'controllers_neox_dashboard' => [
                    'resource'  => [
                        'path'      => '../vendor/xorgxx/neox-dashboard-bundle/src/Controller/',
                        'namespace' => 'NeoxDashBoard\NeoxDashBoardBundle\Controller',
                    ],
                    'type'      => 'attribute',
                    // 'prefix' => '/secure', // Ajoutez un préfixe si nécessaire
                ],
            ];
        }
    }