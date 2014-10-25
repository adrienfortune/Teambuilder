<?php

namespace Tm\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TmUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
