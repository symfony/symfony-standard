<?php

namespace wideweb\CustomExceptionsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExceptionsBundle extends Bundle
{
	public function getParent()
    {
        return 'TwigBundle';
    }
}
