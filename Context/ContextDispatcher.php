<?php

namespace Behat\BehatBundle\Context;

use Behat\Behat\Context\ContextDispatcher as BaseDispatcher;

use Symfony\Component\HttpKernel\HttpKernelInterface;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Symfony2 context dispatcher.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ContextDispatcher extends BaseDispatcher
{
    /**
     * Symfony2 kernel instance.
     *
     * @var     Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * Initialize dispatcher.
     *
     * @param   Symfony\Component\HttpKernel\HttpKernelInterface    $kernel     application kernel
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextParameters()
    {
        return $this->kernel;
    }
}
