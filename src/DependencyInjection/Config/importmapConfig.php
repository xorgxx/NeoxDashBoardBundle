<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config;

    /*    
        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__ . '/../../assets/dist/' => "@xorgxx/neox-dashboard-bundle",
                    __DIR__ . '/../../assets/'       => '@neoxDashBoardAssets'
                ],
            ],
        ]);
    */

    class importmapConfig
    {
        public static function getConfig(): array
        {
            return [
                'imports' => [
                    '@neoxDashBoardAssets/neoxDashBoard' => [
                        'path' => './vendor/xorgxx/neox-dashboard-bundle/assets/neoxDashBoard.js',
                        'entrypoint' => true,
                    ],
                ],
            ];
        }
        
    }