<?php

namespace Behat\BehatBundle;

use Behat\Behat\DependencyInjection\Compiler\ContextReaderPass;
use Behat\Behat\DependencyInjection\Compiler\EventDispatcherPass;
use Behat\Behat\DependencyInjection\Compiler\GherkinPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class BehatBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ContextReaderPass());
        $container->addCompilerPass(new EventDispatcherPass());
        $container->addCompilerPass(new GherkinPass());
    }
}
