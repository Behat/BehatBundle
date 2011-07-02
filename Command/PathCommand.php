<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Command\BehatCommand;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Path testing command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PathCommand extends BehatCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat:path')
            ->setDescription('Tests specified feature(s)')
            ->setDefinition(array_merge(
                array(
                    new InputArgument('features',
                        InputArgument::REQUIRED,
                        'The features path'
                    ),
                ),
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
    protected function getContextClass(InputInterface $input, ContainerInterface $container)
    {
        $featuresPath = explode(':', $input->getArgument('features'));

        $namespacedContext = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            if (false !== strpos(realpath($featuresPath[0]), realpath($bundle->getPath()))) {
                $namespace = str_replace('/', '\\', dirname(str_replace('\\', '/', get_class($bundle))));
                $namespacedContext = $namespace . '\Features\Context\FeatureContext';
                break;
            }
        }

        if (null !== $namespacedContext && class_exists($namespacedContext)) {
            return $namespacedContext;
        }

        return $container->getParameter('behat.context.class');
    }
}
