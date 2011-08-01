<?php

namespace Behat\BehatBundle\Context;

use Behat\Behat\Context\BehatContext as BaseContext;

use Symfony\Component\HttpKernel\HttpKernelInterface;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Symfony2 Behat context.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class BehatContext extends BaseContext
{
    /**
     * Symfony2 kernel instance.
     *
     * @var     Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * Initializes context.
     *
     * @param   Symfony\Component\HttpKernel\HttpKernelInterface   $kernel  application kernel
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns application kernel.
     *
     * @return  Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Returns service container.
     *
     * @return  Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * Returns all context parameters.
     *
     * @return  array
     */
    public function getParameters()
    {
        return $this->getContainer()->getParameter('behat.context.parameters');
    }

    /**
     * Returns context parameter.
     *
     * @param   string  $name
     *
     * @return  mixed
     */
    public function getParameter($name)
    {
        $parameters = $this->getParameters();

        return $parameters[$name];
    }
}
