<?php

namespace Behat\BehatBundle\Environment;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Behat\Behat\Environment\Environment;

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Symfony Browser Environment for Behat.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SymfonyBrowserEnvironment extends Environment
{
    protected $kernel;

    /**
     * Initialize Browser Environment.
     * 
     * @param   Container   $container          container interface
     * @param   array       $kernelOptions      kernel creation options
     * @param   array       $serverParameters   server parameters
     */
    public function __construct($container, array $kernelOptions = array(), array $serverParameters = array())
    {
        $world = $this;
        $class = get_class($container->get('kernel'));

        $this->getClient = function() use ($world, $class, $kernelOptions, $serverParameters) {
            static $client;

            if (null === $client) {
                $client = $world->createClient($class, $kernelOptions, $serverParameters);
            }

            return $client;
        };

        $this->pathTo = function($page) {
            switch ($page) {
                case 'homepage':    return '/';
                default:            return $page;
            }
        };
    }

    /**
     * Create a Client. 
     * 
     * @param   string      $kernelClass        kernel class name
     * @param   array       $kernelOptions      kernel creation options
     * @param   array       $serverParameters   server parameters
     *
     * @return  Client                          A Client instance
     */
    public function createClient($kernelClass, array $kernelOptions = array(), array $serverParameters = array())
    {
        $this->kernel = $this->createKernel($kernelClass, $kernelOptions);
        $this->kernel->boot();

        $client = $this->kernel->getContainer()->get('test.client');
        $client->setServerParameters($serverParameters);
        $client->followRedirects(false);

        return $client;
    }

    /**
     * Create a Kernel. 
     * 
     * @param   string      $class              kernel class
     * @param   array       $options            an array of options
     *
     * @return  HttpKernelInterface a HttpKernelInterface instance
     */
    protected function createKernel($class, array $options = array())
    {
        return new $class(
            isset($options['environment'])  ? $options['environment']   : 'test'
          , isset($options['debug'])        ? $options['debug']         : true
        );
    }
}
