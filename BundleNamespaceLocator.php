<?php

namespace Behat\BehatBundle;

use Symfony\Component\HttpKernel\KernelInterface;

/*
* This file is part of the Behat\BehatBundle.
* (c) Konstantin Kudryashov <ever.zet@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

class BundleNamespaceLocator
{
    /**
     * Kernel instance.
     *
     * @var     Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * Initializes locator.
     *
     * @param   Symfony\Component\HttpKernel\KernelInterface    $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns full namespace path to bundle.
     *
     * @param   string  $bundleName bundle name or path
     *
     * @return  string
     */
    public function findNamespace($bundleName)
    {
        if (false === strpos($bundleName, '\\')) {
            $bundleName = $this->kernel->getBundle($bundleName)->getNamespace();
        } elseif (!preg_match('/Bundle$/', $bundleName)) {
            throw new \InvalidArgumentException('The namespace must end with Bundle.');
        }

        return $bundleName;
    }
}
