<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig;

    use AllowDynamicProperties;
    use DateTime;
    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\ToolsBoxService;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\Routing\RouterInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;
    use Twig\Environment;
    use Twig\Error\LoaderError;
    use Twig\Error\RuntimeError;
    use Twig\Error\SyntaxError;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;
    use Twig\TwigFunction;

    #[AllowDynamicProperties]
    class NeoxDashBroadExtension extends AbstractExtension
    {


        public function __construct(readonly ParameterBagInterface $parameterBag, readonly Environment $twig, readonly TranslatorInterface $translator, readonly ToolsBoxService $toolsBoxService)
        {
        }

        public function getFilters(): array
        {
            return [
                // If your filter generates SAFE HTML, you should add a third
                // parameter: ['is_safe' => ['html']]
                // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
                //            new TwigFilter('setJsFile', [$this, 'setJsFile']),
                new TwigFilter('getShortDomain', $this->shortDomain(...)),
                new TwigFilter('setTimer', $this->setTimer(...)),
                new TwigFilter('setFavorite', $this->setFavorite(...)),
                new TwigFilter('checkSize', $this->checkSize(...)),

            ];
        }

        public function getFunctions(): array
        {
            return [
//            new TwigFunction('getNeoxDashBoard', $this->getNeoxDashBoard(), array(
//                'is_safe'           => array('html'),
//                'needs_environment' => true,
//            )),
//            new TwigFunction('setTimer', [$this, 'setTimer']),
                new TwigFunction('getClassWidgetFavorite', $this->getClassWidgetFavorite(...)),
                new TwigFunction('getClassWidgetSearch', $this->getClassWidgetSearch(...)),
            ];
        }

        public function getClassWidgetFavorite(): ?NeoxDashClass
        {
            return $this->toolsBoxService->getNeoxFavorite();
        }

        public function getClassWidgetSearch(): ?NeoxDashClass
        {
            return $this->toolsBoxService->getNeoxSearch();
        }

        public function checkSize(?NeoxDashClass $entity): bool
        {
            if ($entity) {
                $size = $entity->getSize()->value ?? null;

                // Check if the size contains a number and capture it
                if ($size !== null && preg_match('/\d+/', $size, $matches)) {
                    $number = (int)$matches[ 0 ]; // Convert the captured number to an integer
                    return $number <= 6; // Return true if the number is less than or equal to 6
                }
            }


            return false; // Return false if no number is found
        }

        public function setFavorite(neoxDashDomain $entity): bool
        {
            if ($entity->getFavorite() !== null) {
                return $entity
                    ->getFavorite()
                    ->getFavorite()
                ;
            }
            return false;
        }

        public function setTimer($timer = null)
        {
            return match ($timer) {
                null => 500000,
                '0' => 500000,
                0 => 500000,
                default => $timer,
            };

        }

        public function shortDomain(string $url, $callback = "first"): array
        {
            // Check if the URL starts with a scheme
            if (!preg_match('/^http(s)?:\/\//', $url)) {
                // Prepend 'http://' if no scheme is present
                $url = 'https://' . $url;
            }
            $domain = parse_url($url, PHP_URL_HOST);
            $parts  = explode('.', $domain);

            if (count($parts) >= 2) {
                $mainDomain = $parts[ count($parts) - 2 ];
            } else {
                $mainDomain = $parts[ 0 ];
            }

            // Extraire la première lettre du domaine principal
            $firstLetter = strtolower(substr($mainDomain, 0, 1));
            $icon        = "mynaui:daze-square";
            // Check if it's a letter (e.g., for domain names)
            if (ctype_alpha($firstLetter)) {
                $icon = "mynaui:letter-$firstLetter-diamond";
            } // Check if it's a digit (e.g., for IP addresses)
            elseif (ctype_digit($firstLetter)) {
                $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $words     = $formatter->format($firstLetter);
                $icon      = "mynaui:$words-diamond";
            }


            $a = [
                'url'         => $url,
                'domain'      => $domain,
                'shortDomain' => $mainDomain,
                'first'       => $firstLetter,
                'icon'        => $icon
            ];

            return $a;
        }
    }