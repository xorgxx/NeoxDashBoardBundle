<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Factory;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
    use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

    /**
     * @extends PersistentProxyObjectFactory<NeoxDashSection>
     */
    final class NeoxDashSectionFactory extends PersistentProxyObjectFactory
    {
        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
         *
         * @todo inject services if required
         */
        public function __construct()
        {
        }

        public static function class(): string
        {
            return NeoxDashSection::class;
        }

        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
         *
         * @todo add your default values here
         */
        protected function defaults(): array|callable
        {
            // Liste de choix
            $categories = [ 'Web', 'Web dev', 'Misc', 'Technologie', 'Santé', 'Finance', 'Éducation', 'Voyage' ];

            return [
                'colonne' => self::faker()->Numberbetween(1, 5),
                'name'    => self::faker()->word(1),
                'row'     => self::faker()->Numberbetween(1,4),
                'class'   => NeoxDashClassFactory::randomOrCreate(),
            ];
        }

        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
         */
        protected function initialize(): static
        {
            return $this// ->afterInstantiate(function(NeoxDashSection $neoxDashSection): void {})
                ;
        }
    }
