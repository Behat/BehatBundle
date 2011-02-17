<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

use Behat\Behat\Console\Command\BehatCommand;

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
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
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Tests specified bundle features')
            ->setDefinition(array(
                new InputArgument('namespace',
                    InputArgument::REQUIRED,
                    'The bundle namespace'
                ),
                new InputOption('--config',         '-c',
                    InputOption::VALUE_REQUIRED,
                    'Specify external configuration file to load (behat.yml & config/behat.yml will be used in other way).'
                ),
                new InputOption('--out',            null,
                    InputOption::VALUE_REQUIRED,
                    'Write formatter output to a file/directory instead of STDOUT.'
                ),
                new InputOption('--name',           null,
                    InputOption::VALUE_REQUIRED,
                    'Only execute the feature elements (features or scenarios) which match part of the given name.'
                ),
                new InputOption('--tags',           '-t',
                    InputOption::VALUE_REQUIRED,
                    'Only execute the features or scenarios with tags matching expression.'
                ),
                new InputOption('--strict',         null,
                    InputOption::VALUE_NONE,
                    'Fail if there are any undefined or pending steps.'
                ),


                new InputOption('--usage',          null,
                    InputOption::VALUE_NONE,
                    'Print *.feature example in specified language (--lang).'
                ),
                new InputOption('--steps',          null,
                    InputOption::VALUE_NONE,
                    'Print available steps in specified language (--lang).'
                ),


                new InputOption('--format',         '-f',
                    InputOption::VALUE_REQUIRED,
                    'How to format features (Default: pretty). Available formats is pretty, progress, html.'
                ),
                new InputOption('--colors',         null,
                    InputOption::VALUE_NONE,
                    'Force Behat to use ANSI color in the output.'
                ),
                new InputOption('--no-colors',      '-C',
                    InputOption::VALUE_NONE,
                    'Do not use ANSI color in the output.'
                ),
                new InputOption('--no-time',        '-T',
                    InputOption::VALUE_NONE,
                    'Hide time in output.'
                ),
                new InputOption('--lang',           null,
                    InputOption::VALUE_REQUIRED,
                    'Print formatters output in particular language.'
                ),
                new InputOption('--no-multiline',   null,
                    InputOption::VALUE_NONE,
                    'No multiline arguments in output.'
                ),
            ))
            ->setName('behat:test:bundle')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer($configFile = null)
    {
        return $this->application->getKernel()->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    protected function locateFeaturesPaths(InputInterface $input, ContainerInterface $container)
    {
        if (!preg_match('/Bundle$/', $namespace = $input->getArgument('namespace'))) {
            throw new \InvalidArgumentException('The namespace must end with Bundle.');
        }

        $basePath = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            $tmp = str_replace('\\', '/', get_class($bundle));
            $bundleNamespace = str_replace('/', '\\', dirname($tmp));
            if ($namespace === $bundleNamespace) {
                $basePath = realpath($bundle->getPath());
                break;
            }
        }

        if (null === $basePath) {
            throw new \InvalidArgumentException(
                sprintf("Unable to test bundle (%s is not a defined namespace).", $namespace)
            );
        }

        $this->pathTokens['BEHAT_BUNDLE_PATH'] = dirname(__DIR__);
        $this->pathTokens['BEHAT_WORK_PATH'] = $this->preparePath($basePath . '/Tests');
        $this->pathTokens['BEHAT_BASE_PATH'] = $this->preparePath($basePath . '/Tests/Features');
        $featuresPath = $this->preparePath($container->getParameter('behat.paths.features'));

        if ('.feature' !== mb_substr($featuresPath, -8)) {
            $finder         = new Finder();
            $featuresPaths  = $finder->files()->name('*.feature')->in($featuresPath);
        } else {
            $featuresPaths  = (array) ($featuresPath . $lineFilter);
        }

        return $featuresPaths;
    }
}
