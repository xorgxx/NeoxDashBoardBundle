<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Factory;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

    /**
     * @extends PersistentProxyObjectFactory<NeoxDashSetup>
     */
    final class NeoxDashSetupFactory extends PersistentProxyObjectFactory
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
            return NeoxDashSetup::class;
        }


        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
         *
         * @todo add your default values here
         */
        protected function defaults(): array|callable
        {

            [ $url, $domain ] = $this->shortSetup();
            return [
                "country" => "fr",
                "theme"   => "#d5cdcd",
            ];
        }

        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
         */
        protected function initialize(): static
        {
            return $this// ->afterInstantiate(function(NeoxDashSetup $neoxDashSetup): void {})
                ;
        }


        /**
         * @return array
         */
        private function shortSetup(): array
        {
            $url    = self::faker()->url();
            $domain = parse_url($url, PHP_URL_HOST);
            $parts  = explode('.', $domain);

            if (count($parts) >= 2) {
                $mainSetup = $parts[ count($parts) - 2 ];
            } else {
                $mainSetup = $parts[ 0 ];
            }

            // Extraire la première lettre du domaine principal
            $firstLetter = substr($mainSetup, 0, 1);

            return [ $url, $domain ];
        }
    }
