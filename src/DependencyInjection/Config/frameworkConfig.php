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

    class frameworkConfig
    {
        public static function getConfig(): array
        {
            return [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__ . '/../../../assets/dist/' => "@xorgxx/neox-dashboard-bundle",
                    ],
                ],
            ];
        }
    }