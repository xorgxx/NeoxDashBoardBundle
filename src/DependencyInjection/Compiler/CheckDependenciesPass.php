<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\DependencyInjection\Compiler;

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use RuntimeException;

    class CheckDependenciesPass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container): void
        {
            // Check if the Twig service is available
            if (!$container->has('twig')) {
                throw new RuntimeException('TwigBundle is not installed. Please install it to use NeoxDashBoardBundle.');
            }

            // Check if the AssetMapper is available
            if (!$container->has(AssetMapperInterface::class)) {
                throw new RuntimeException('AssetMapper is not available. Please install the required bundle.');
            }

            // Check if the Doctrine ORM service is available
            if (!$container->has('doctrine.orm.entity_manager.default')) {
                throw new RuntimeException('Doctrine ORM is not installed. Please install DoctrineBundle to use NeoxDashBoardBundle.');
            }

            // Add more checks as needed for other services or bundles
        }
    }
