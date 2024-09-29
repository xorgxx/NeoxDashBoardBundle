<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Factory;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

    /**
     * @extends PersistentProxyObjectFactory<NeoxDashDomain>
     */
    final class NeoxDashDomainFactory extends PersistentProxyObjectFactory
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
            return NeoxDashDomain::class;
        }


        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
         *
         * @todo add your default values here
         */
        protected function defaults(): array|callable
        {

            [ $url, $domain ] = $this->shortDomain();
            return [
                'name'        => $domain, # ;self::faker()->domainWord(),
                'section'     => NeoxDashSectionFactory::random(),
                'slug'        => self::faker()->slug(1),
                'url'         => $url,
                'color'       => self::faker()->hexColor(),

            ];
        }

        /**
         * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
         */
        protected function initialize(): static
        {
            return $this// ->afterInstantiate(function(NeoxDashDomain $neoxDashDomain): void {})
                ;
        }


        /**
         * @return array
         */
        private function shortDomain(): array
        {
            $url    = self::faker()->url();
            $domain = parse_url($url, PHP_URL_HOST);
            $parts  = explode('.', $domain);

            if (count($parts) >= 2) {
                $mainDomain = $parts[ count($parts) - 2 ];
            } else {
                $mainDomain = $parts[ 0 ];
            }

            // Extraire la première lettre du domaine principal
            $firstLetter = substr($mainDomain, 0, 1);

            return [ $url, $domain ];
        }
    }
