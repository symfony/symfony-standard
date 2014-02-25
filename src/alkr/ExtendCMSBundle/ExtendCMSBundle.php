<?php

namespace alkr\ExtendCMSBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExtendCMSBundle extends Bundle
{
	public function getParent()
    {
        return 'CMSBundle';
    }
}
