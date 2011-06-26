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
     * Context class name.
     *
     * @var     string
     */
    private $contextClassName;
    /**
     * Symfony2 kernel instance.
     *
     * @var     Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * Initialize dispatcher.
     *
     * @param   string                                              $contextClassName   context class name
     * @param   Symfony\Component\HttpKernel\HttpKernelInterface    $kernel             application kernel
     */
    public function __construct($contextClassName, HttpKernelInterface $kernel)
    {
        $this->contextClassName = $contextClassName;
        $this->kernel           = $kernel;

        if (!class_exists($this->contextClassName)) {
            throw new \InvalidArgumentException(sprintf(
                'Context class "%s" not found', $this->contextClassName
            ));
        }

        $contextClassRefl = new \ReflectionClass($contextClassName);
        if (!$contextClassRefl->implementsInterface('Behat\Behat\Context\ContextInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'Context class "%s" should implement ContextInterface', $this->contextClassName
            ));
        }
    }

    /**
     * Create new context instance.
     *
     * @return  Behat\Behat\Context\ContextInterface
     */
    public function createContext()
    {
        return new $this->contextClassName($this->kernel);
    }
}
