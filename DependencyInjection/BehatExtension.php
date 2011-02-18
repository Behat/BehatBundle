<?php

namespace Behat\BehatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Behat\Behat\DependencyInjection\BehatExtension as BaseExtension;

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat extension for DIC.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class BehatExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadDefaults($container)
    {
        $behatClassLoaderReflection = new \ReflectionClass('Behat\Behat\Console\BehatApplication');
        $gherkinParserReflection    = new \ReflectionClass('Behat\Gherkin\Parser');

        $behatLibPath   = realpath(dirname($behatClassLoaderReflection->getFilename()) . '/../../../../');
        $gherkinLibPath = realpath(dirname($gherkinParserReflection->getFilename()) . '/../../../');

        $loader = new XmlFileLoader($container, new FileLocator($behatLibPath . '/src/Behat/Behat/DependencyInjection/config'));
        $loader->load('behat.xml');
        $loader->load(__DIR__ . '/../Resources/config/behat_bundle.xml');

        $container->setParameter('gherkin.paths.lib', $gherkinLibPath);
        $container->setParameter('behat.paths.lib', $behatLibPath);
    }
}
