<?php

namespace Behat\BehatBundle\Console\Processor\Bundle;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\LocatorProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bundle locator processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class LocatorProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        $locator    = $container->get('behat.path_locator');
        $bundlePath = $this->locateBundlePath($container, $input);

        $locator->locateBasePath($bundlePath.DIRECTORY_SEPARATOR.'Features');

        if (!($input->hasOption('init') && $input->getOption('init'))
         && !is_dir($featuresPath = $locator->getFeaturesPath())) {
            throw new \InvalidArgumentException("Features path \"$featuresPath\" does not exist");
        }
    }

    /**
     * Locate current bundle path.
     *
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container  service container
     * @param   Symfony\Component\Console\Input\InputInterface              $input      input instance
     *
     * @return  string
     */
    protected function locateBundlePath(ContainerInterface $container, InputInterface $input)
    {
        $locator    = $container->get('behat_bundle.namespace_locator');
        $namespace  = $locator->findNamespace($input->getArgument('namespace'));

        $bundlePath = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            $tmp = str_replace('\\', '/', get_class($bundle));
            $bundleNamespace = str_replace('/', '\\', dirname($tmp));
            if ($namespace === $bundleNamespace) {
                $bundlePath = realpath($bundle->getPath());
                break;
            }
        }

        if (null === $bundlePath) {
            throw new \InvalidArgumentException(
                sprintf("Unable to test bundle (%s is not a defined namespace).", $namespace)
            );
        }

        return $bundlePath;
    }
}
