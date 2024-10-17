<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Twig;

use AllowDynamicProperties;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
    private TranslatorInterface    $translator;
    private RouterInterface        $router;
    private EntityManagerInterface $entityManager;


    public function __construct(ParameterBagInterface $parameterBag, Environment $twig, TranslatorInterface $translator)
    {
        $this->parameterBag = $parameterBag;
        $this->twig         = $twig;
        $this->translator   = $translator;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
//            new TwigFilter('setJsFile', [$this, 'setJsFile']),
            new TwigFilter('getShortDomain', [$this, 'shortDomain']),
            new TwigFilter('setTimer', [$this, 'setTimer']),
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
        ];
    }

    public function setTimer( $timer = null)
    {
        return match ($timer) {
            null        => 500000,
            '0'         => 500000,
            0           => 500000,
            default     => $timer,
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
        $firstLetter = substr($mainDomain, 0, 1);
        $a = [
            'url'       => $url,
            'domain'    => $domain,
            'first'     => $firstLetter
        ];
        
        return $a;
    }
}