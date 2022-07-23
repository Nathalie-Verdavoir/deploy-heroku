<?php

namespace Nat\DeployBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NatDeployBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}