<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Pattern;

    use NeoxDashBoard\NeoxDashBoardBundle\entity\NeoxDashSetup;

    class SetupHelper
    {


        public function initSeput(?NeoxDashSetup $neoxDashSetup = null): array
        {
            $setups = [
                "logo"    => $neoxDashSetup->getLogo() ?? "tttp.png",
                "weather" => $neoxDashSetup->getWeather() ?? null,
                "country" => $neoxDashSetup->getCountry() ?? "fr",
            ];
            return $setups;
        }
    }