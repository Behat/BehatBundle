<?php

namespace Behat\BehatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/*
* This file is part of the BehatBundle.
* (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

class BehatBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}
