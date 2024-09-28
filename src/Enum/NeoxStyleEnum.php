<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Enum;


    # src/Enum/AppEnvironment.php
    use Symfony\Contracts\Translation\TranslatableInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;

    enum NeoxStyleEnum: string implements TranslatableInterface
    {
        case ACCORDION      = 'Accordion';
        case TABS           = 'Tabs';

        public function trans(TranslatorInterface $translator, ?string $locale = null): string
        {
            // Translate enum from name (Left, Center or Right)
            return $translator->trans($this->name, locale: $locale);

            // Translate enum using custom labels
            return match ($this) {
                self::GOOGLE    => $translator->trans('text_align.left.label', locale: $locale),
                self::BRING     => $translator->trans('text_align.center.label', locale: $locale),
            };
        }
    }