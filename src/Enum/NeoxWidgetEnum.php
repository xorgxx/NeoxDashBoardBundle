<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Enum;


    # src/Enum/AppEnvironment.php
    use Symfony\Contracts\Translation\TranslatableInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;

    enum NeoxWidgetEnum: string implements TranslatableInterface
    {
        case FAVORITE      = 'Favorite';
        case SEARCH        = 'Search';

        public function trans(TranslatorInterface $translator, ?string $locale = null): string
        {
            // Translate enum from name (Left, Center or Right)
            return $translator->trans($this->name, locale: $locale);

            // Translate enum using custom labels
//            return match ($this) {
//                self::ACCORDION    => $translator->trans('text_align.left.label', locale: $locale),
//                self::TABS     => $translator->trans('text_align.center.label', locale: $locale),
//            };
        }
    }