<?php

namespace Behat\BehatBundle\Environment;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Behat\Behat\Environment\Environment;

use Behat\Mink\Mink;

/*
 * This file is part of the BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink Browser Environment for Behat.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkEnvironment extends Environment
{
    private $mink;
    private $kernel;

    /**
     * Initialize mink browser environment.
     *
     * @param   string                                              $startUrl   browser start url
     * @param   Behat\Mink\Mink                                     $mink       mink
     * @param   Symfony\Component\HttpKernel\HttpKernelInterface    $kernel     application kernel
     */
    public function __construct($startUrl, Mink $mink, HttpKernelInterface $kernel)
    {
        $this->mink     = $mink;
        $this->kernel   = $kernel;

        $this->getPathTo = function($path) use($startUrl) {
            return 0 !== strpos('http', $path) ? $startUrl . ltrim($path, '/') : $path;
        };
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
     * Returns mink instance.
     *
     * @return  Behat\Mink\Mink
     */
    public function getMink()
    {
        return $this->mink;
    }

    /**
     * Returns current mink session instance.
     *
     * @return  Behat\Mink\Session
     */
    public function getSession()
    {
        return $this->getMink()->getSession();
    }
}
