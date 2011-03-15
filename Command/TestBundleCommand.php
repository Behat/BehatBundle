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
    private $bundlePath;

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
                new InputOption('--out',            null,
                    InputOption::VALUE_REQUIRED,
                    '          ' .
                    'Write formatter output to a file/directory instead of STDOUT.'
                ),
                new InputOption('--name',           null,
                    InputOption::VALUE_REQUIRED,
                    '         ' .
                    'Only execute the feature elements (features or scenarios) which match part of the given name or regex.'
                ),
                new InputOption('--tags',           null,
                    InputOption::VALUE_REQUIRED,
                    '         ' .
                    'Only execute the features or scenarios with tags matching tag filter expression.'
                ),
                new InputOption('--strict',         null,
                    InputOption::VALUE_NONE,
                    '       ' .
                    'Fail if there are any undefined or pending steps.'
                ),


                new InputOption('--init',           null,
                    InputOption::VALUE_NONE,
                    '         ' .
                    'Create features/ directory structure'
                ),
                new InputOption('--usage',          null,
                    InputOption::VALUE_NONE,
                    '        ' .
                    'Print *.feature example in specified language (--lang).'
                ),
                new InputOption('--steps',          null,
                    InputOption::VALUE_NONE,
                    '        ' .
                    'Print available steps in specified language (--lang).'
                ),


                new InputOption('--format',         '-f',
                    InputOption::VALUE_REQUIRED,
                    '  ' .
                    'How to format features (Default: pretty). Available formats are ' .
                    implode(', ',
                        array_map(function($name) {
                            return "<info>$name</info>";
                        }, array_keys($this->defaultFormatters))
                    )
                ),
                new InputOption('--colors',         null,
                    InputOption::VALUE_NONE,
                    '       ' .
                    'Force Behat to use ANSI color in the output.'
                ),
                new InputOption('--no-colors',      null,
                    InputOption::VALUE_NONE,
                    '    ' .
                    'Do not use ANSI color in the output.'
                ),
                new InputOption('--no-time',        null,
                    InputOption::VALUE_NONE,
                    '      ' .
                    'Hide time in output.'
                ),
                new InputOption('--lang',           null,
                    InputOption::VALUE_REQUIRED,
                    '         ' .
                    'Print formatter output in particular language.'
                ),
                new InputOption('--no-multiline',   null,
                    InputOption::VALUE_NONE,
                    ' ' .
                    'No multiline arguments in output.'
                ),
            ))
            ->setName('behat:test:bundle')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container          = $this->configureContainer();
        $this->bundlePath   = $this->locateBundlePath($input, $container);

        if ($input->getOption('usage')) {
            $this->printUsageExample($input, $container, $output);

            return 0;
        }

        if ($input->getOption('init')) {
            $this->createFeaturesPath($container, $output);

            return 0;
        }

        $featuresPaths = $this->locateFeaturesPaths($input, $container);
        $this->loadBootstraps($container);
        $formatter = $this->configureFormatter($input, $container, $output->isDecorated());
        $this->configureGherkinParser($input, $container);
        $this->configureDefinitionDispatcher($input, $container);

        if ($input->getOption('steps')) {
            $this->printAvailableSteps($input, $container, $output);

            return 0;
        }

        $this->configureHookDispatcher($input, $container);
        $this->configureEnvironmentBuilder($input, $container);
        $this->configureEventDispathcer($formatter, $container);

        $result = $this->runFeatures($featuresPaths, $container);

        if ($input->getOption('strict')) {
            return intval(0 < $result);
        } else {
            return intval(4 === $result);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer($configFile = null)
    {
        return $this->getApplication()->getKernel()->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    protected function locateFeaturesPaths(InputInterface $input, ContainerInterface $container)
    {
        $this->pathTokens['BEHAT_BUNDLE_PATH'] = dirname(__DIR__);
        $this->pathTokens['BEHAT_WORK_PATH'] = $this->preparePath(
            $this->bundlePath . DIRECTORY_SEPARATOR . 'Tests'
        );
        $this->pathTokens['BEHAT_BASE_PATH'] = $this->preparePath(
            $this->bundlePath . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Features'
        );
        $featuresPath = $this->preparePath($container->getParameter('behat.paths.features'));

        if ('.feature' !== mb_substr($featuresPath, -8)) {
            $finder         = new Finder();
            $featuresPaths  = $finder->files()->name('*.feature')->in($featuresPath);
        } else {
            $featuresPaths  = (array) ($featuresPath . $lineFilter);
        }

        return $featuresPaths;
    }

    /**
     * Creates features path structure (initializes behat tests structure).
     *
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container  service container
     * @param   Symfony\Component\Console\Input\OutputInterface             $output     output console
     */
    protected function createFeaturesPath(ContainerInterface $container, OutputInterface $output)
    {
        $this->pathTokens['BEHAT_WORK_PATH'] = $this->preparePath(
            $this->bundlePath . DIRECTORY_SEPARATOR . 'Tests', true
        );

        parent::createFeaturesPath($container, $output);
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

        return $basePath;
    }
}
