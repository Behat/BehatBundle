<?php

namespace Behat\BehatBundle\Context;

use Behat\Mink\Mink,
    Behat\Mink\Behat\Context\MinkContext as BaseContext;

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
abstract class MinkContext extends BaseContext
{
    /**
     * Symfony2 kernel instance.
     *
     * @var     Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;
    /**
     * Mink instance.
     *
     * @var     Behat\Mink\Mink
     */
    private static $mink;

    /**
     * Initializes context.
     *
     * @param   Symfony\Component\HttpKernel\HttpKernelInterface   $kernel  application kernel
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;

        if (null === self::$mink) {
            self::$mink = $this->getContainer()->get('behat.mink');
            $this->registerSessions(self::$mink);
        }
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
     * Returns Mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return self::$mink;
    }

    /**
     * Registers additional Mink sessions.
     *
     * @param   Behat\Mink\Mink     $mink   Mink manager instance
     */
    protected function registerSessions(Mink $mink)
    {
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
        if ($this->getContainer()->hasParameter("behat.mink.$name")) {
            return $this->getContainer()->getParameter("behat.mink.$name");
        }

        $parameters = $this->getParameters();

        return $parameters[$name];
    }
}
