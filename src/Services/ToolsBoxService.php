<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;

    use Random\RandomException;

    class ToolsBoxService
    {


        public function __construct()
        {

        }

        /**
         * @throws RandomException
         */
        public static function getColor(): string
        {
            $r = random_int(0, 127); // Rouge
            $g = random_int(0, 127); // Vert
            $b = random_int(0, 127); // Bleu

            return sprintf("#%02x%02x%02x", $r, $g, $b);
        }
    }