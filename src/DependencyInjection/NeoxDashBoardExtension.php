<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class NeoxDashBoardExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // set key config as container parameters
        // foreach ($config as $key => $value) {
        //    $container->setParameter( 'neox_dashboard.' . $key, $value);
        // }
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$this->isAssetMapperAvailable($container)) {
            return;
        }
        $container->prependExtensionConfig('twig', [
            'paths' => [
                '%kernel.project_dir%\\vendor\\xorgxx\\neox-dashboard-bundle\\src\\Templates' => 'NeoxDashBoardBundle',
            ],
        ]);
        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                '%kernel.project_dir%\\vendor\\xorgxx\\neox-dashboard-bundle\\src\\Twig\\Components\\' => '~',
            ],
        ]);
        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__.'/../../assets/dist/' => "@xorgxx/neox-dashboard-bundle",
//                    '%kernel.project_dir%/vendor/xorgxx/neox-dashboard-bundle/assets/dist/',
                ],
            ],
        ]);
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}
