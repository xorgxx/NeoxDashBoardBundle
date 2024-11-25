<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Enum;


    # src/Enum/AppEnvironment.php
    use Symfony\Contracts\Translation\TranslatableInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;

    enum NeoxSizeEnum: string implements TranslatableInterface
    {
        case COL1  = 'col-md-1';
        case COL2  = 'col-md-2';
        case COL3  = 'col-md-3';
        case COL4  = 'col-md-4';
        case COL5  = 'col-md-5';
        case COL6  = 'col-md-6';
        case COL7  = 'col-md-7';
        case COL8  = 'col-md-8';
        case COL9  = 'col-md-9';
        case COL10 = 'col-md-10';
        case COL11 = 'col-md-11';
        case COL12 = 'col-md-12';

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