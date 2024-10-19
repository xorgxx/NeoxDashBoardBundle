<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection;

    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Compiler\CheckDependenciesPass;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\frameworkConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\twigComponentsConfig;
    use NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Config\twigConfig;
    use Symfony\Component\AssetMapper\AssetMapperInterface;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
    use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

    class NeoxDashBoardExtension extends Extension implements PrependExtensionInterface
    {

        public function build(ContainerBuilder $container): void
        {
            parent::build($container);
            $container->addCompilerPass(new CheckDependenciesPass());
        }

        public function prepend(ContainerBuilder $container): void
        {
            if (!$this->isAssetMapperAvailable($container)) {
                return;
            }

            $container->prependExtensionConfig('twig', TwigConfig::getConfig());
            $container->prependExtensionConfig('twig_component', twigComponentsConfig::getConfig() );
            $container->prependExtensionConfig('framework', frameworkConfig::getConfig() );

        }

        /**
         * @throws \Exception
         */
        public function load(array $configs, ContainerBuilder $container): void
        {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
            // Load each YAML file separately
            $loader->load('services.yaml');

            ;
//        $loader->load('routes.yaml');

            $configuration = $this->getConfiguration($configs, $container);
            $config        = $this->processConfiguration($configuration, $configs);

            // set key config as container parameters
            // foreach ($config as $key => $value) {
            //    $container->setParameter( 'neox_dashboard.' . $key, $value);
            // }


            // Ajoutez le chemin des traductions
            $container->setParameter('translator.paths', [
                '%kernel.project_dir%/src/NeoxDashboardBundle/translations',
            ]);
        }




        private function isAssetMapperAvailable(ContainerBuilder $container): bool
        {
            if (!interface_exists(AssetMapperInterface::class)) {
                return false;
            }

            // check that FrameworkBundle 6.3 or higher is installed
            $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
            if (!isset($bundlesMetadata[ 'FrameworkBundle' ])) {
                return false;
            }

            return is_file($bundlesMetadata[ 'FrameworkBundle' ][ 'path' ] . '/Resources/config/asset_mapper.php');
        }

    }
