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
        ];
    }

    public function getFunctions(): array
    {
        return [
//            new TwigFunction('getNeoxDashBoard', $this->getNeoxDashBoard(), array(
//                'is_safe'           => array('html'),
//                'needs_environment' => true,
//            )),
//            new TwigFunction('setJsFile', [$this, 'setJsFile']),
//            new TwigFunction('getPropertyType', [ReflectionHelper::class, 'getPropertyType']),
//            new TwigFunction('getTrans', [$this, 'getTranslation']),

        ];
    }

}