<?php

namespace NeoxDashBoard\NeoxDashBoardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NeoxDashBoardBundle extends Bundle
{

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
