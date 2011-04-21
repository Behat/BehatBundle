<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Finder\Finder;

use Behat\Behat\Console\Command\BehatCommand,
    Behat\Behat\PathLocator;

/*
 * This file is part of the BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bundle Test Command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class TestBundleCommand extends BehatCommand
{
    private $bundlePath;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat:test:bundle')
            ->setDescription('Tests specified bundle features')
            ->setDefinition(array_merge(
                array(
                    new InputArgument('namespace',
                        InputArgument::REQUIRED,
                        'The bundle namespace'
                    ),
                ),
                $this->getInitOptions(),
                $this->getDemonstrationOptions(),
                $this->getFilterOptions(),
                $this->getFormatterOptions(),
                $this->getRunOptions()
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function createContainer($configFile = null, $profile = null)
    {
        return $this->getApplication()->getKernel()->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    protected function locateBasePath(PathLocator $locator, InputInterface $input)
    {
        $bundlePath = $this->locateBundlePath($input, $this->createContainer());

        return $locator->locateBasePath($bundlePath . DIRECTORY_SEPARATOR . 'Features');
    }

    /**
     * Locate current bundle path.
     *
     * @param   Symfony\Component\Console\Input\InputInterface              $input      input instance
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container  service container
     *
     * @return  string
     */
    protected function locateBundlePath(InputInterface $input, ContainerInterface $container)
    {
        if (!preg_match('/Bundle$/', $namespace = $input->getArgument('namespace'))) {
            throw new \InvalidArgumentException('The namespace must end with Bundle.');
        }

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
