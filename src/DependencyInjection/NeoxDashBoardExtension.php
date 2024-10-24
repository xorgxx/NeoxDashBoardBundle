<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection;

    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\doctrineExtensionsConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\frameworkConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\importmapConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\routerConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\stofDoctrineExtensionsConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\twigComponentsConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\twigConfig;
    use Symfony\Component\AssetMapper\AssetMapperInterface;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
    use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
    use RuntimeException;
    use Symfony\Component\Filesystem\Filesystem;

    class NeoxDashBoardExtension extends Extension implements PrependExtensionInterface
    {
        public function build(ContainerBuilder $container): void
        {
            parent::build($container);
            $this->checkDependencies($container);

            // Register Gedmo mappings
//            $this->registerGedmoMappings($container);
        }

        private function addImportMapConfiguration(ContainerBuilder $container): void
        {
            $projectDir = $container->getParameter('kernel.project_dir');
            $importmapPath = $projectDir . '/importmap.php';

            if (!file_exists($importmapPath)) {
                throw new \RuntimeException('Le fichier importmap.php est introuvable.');
            }

            $content = file_get_contents($importmapPath);
            $entry = <<<PHP
    '@neoxDashBoardAssets/neoxDashBoard' => [
        'path' => './vendor/xorgxx/neox-dashboard-bundle/assets/neoxDashBoard.js',
        'entrypoint' => true,
    ],
PHP;

            // Vérifier si l'entrée existe déjà pour éviter les doublons
            if (!str_contains($content, '@neoxDashBoardAssets/neoxDashBoard')) {
                // Ajouter l'entrée après 'app' => [...]
                $newContent = preg_replace(
                    "/('app' => \[.*?\],)/s",
                    "$1\n$entry",
                    $content
                );

                $filesystem = new Filesystem();
                $filesystem->dumpFile($importmapPath, $newContent);
            }
        }

        // Not use yet. maybe will need later so quipe for now !!!
        private function registerGedmoMappings(ContainerBuilder $container): void
        {
            $container->setParameter('doctrine.orm.default_metadata_driver', 'doctrine.orm.default_metadata_driver');

            // Register the Gedmo mappings as a service
            $container->register('doctrine.orm.default_metadata_driver', 'Doctrine\ORM\Mapping\Driver\AnnotationDriver')
                      ->addArgument(new \Doctrine\Common\Annotations\AnnotationReader())
                      ->addArgument(['%kernel.project_dir%/vendor/gedmo/doctrine-extensions/src/Translatable/Entity'])
                      ->addArgument(['%kernel.project_dir%/vendor/gedmo/doctrine-extensions/src/Translator/Entity'])
                      ->addArgument(['%kernel.project_dir%/vendor/gedmo/doctrine-extensions/src/Loggable/Entity'])
                      ->addArgument(['%kernel.project_dir%/vendor/gedmo/doctrine-extensions/src/Tree/Entity']);
        }

        public function prepend(ContainerBuilder $container): void
        {
            if ($this->isAssetMapperAvailable($container)) {
                $this->prependConfigurations($container);
            }

        }

        private function prependConfigurations(ContainerBuilder $container): void
        {
            $configurations = [
                'twig'                     => TwigConfig::getConfig(),
//                'importmap'                => importmapConfig::getConfig(),
                'twig_components'          => twigComponentsConfig::getConfig(),
                'framework'                => frameworkConfig::getConfig(),
                'router'                   => routerConfig::getConfig(),
                'doctrine'                 => doctrineExtensionsConfig::getConfig(),
                'stof_doctrine_extensions' => stofDoctrineExtensionsConfig::getConfig()
            ];

            foreach ($configurations as $extension => $config) {
                $container->prependExtensionConfig($extension, $config);
            }

            // Set translation paths
            $container->setParameter('translator.paths', [
                '%kernel.project_dir%/src/NeoxDashboardBundle/translations',
            ]);
        }

        /**
         * @throws \Exception
         */
        public function load(array $configs, ContainerBuilder $container): void
        {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
            $loader->load('services.yaml');
            // Uncomment if needed
            // $loader->load('routes.yaml');

            $configuration = $this->getConfiguration($configs, $container);
            $this->processConfiguration($configuration, $configs);

            $this->addImportMapConfiguration($container);
        }

        private function isAssetMapperAvailable(ContainerBuilder $container): bool
        {
            $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');

            return interface_exists(AssetMapperInterface::class) && isset($bundlesMetadata[ 'FrameworkBundle' ]) && is_file($bundlesMetadata[ 'FrameworkBundle' ][ 'path' ] . '/Resources/config/asset_mapper.php');
        }

        private function checkDependencies(ContainerBuilder $container): void
        {
            $dependencies = [
                'twig'                                => 'TwigBundle is not installed. Please install it to use NeoxDashBoardBundle.',
                'twig.components'                     => 'Twig components are not available. Please install them to use NeoxDashBoardBundle.',
                AssetMapperInterface::class           => 'AssetMapper is not available. Please install the required bundle.',
                'doctrine.orm.entity_manager.default' => 'Doctrine ORM is not installed. Please install DoctrineBundle to use NeoxDashBoardBundle.',
                'stof_doctrine_extensions'            => 'StofDoctrineExtensionsBundle is not installed. Please install it to use NeoxDashBoardBundle.',
            ];

            foreach ($dependencies as $service => $errorMessage) {
                if (!$container->has($service) && !$container->hasDefinition($service)) {
                    throw new RuntimeException($errorMessage);
                }
            }
        }

    }
