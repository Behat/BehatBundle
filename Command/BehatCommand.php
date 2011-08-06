<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Command\BehatCommand as BaseCommand,
    Behat\Behat\Console\Processor,
    Behat\Behat\Event\SuiteEvent;

use Behat\BehatBundle\Console\Processor as BundleProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat testing command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class BehatCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat')
            ->setDescription('Tests Behat feature(s) in specified bundle')
            ->setProcessors(array(
                new BundleProcessor\LocatorProcessor(),
                new BundleProcessor\InitProcessor(),
                new BundleProcessor\ContextProcessor(),
                new Processor\FormatProcessor(),
                new BundleProcessor\HelpProcessor(),
                new Processor\GherkinProcessor(),
                new BundleProcessor\RerunProcessor(),
            ))
            ->addArgument('features', InputArgument::OPTIONAL,
                "Feature(s) to run. Could be:".
                "\n- a dir (<comment>src/to/Bundle/Features/</comment>), " .
                "\n- a feature (<comment>src/to/Bundle/Features/*.feature</comment>), " .
                "\n- a scenario at specific line (<comment>src/to/Bundle/Features/*.feature:10</comment>). " .
                "\n- Also, you can use short bundle notation (<comment>@BundleName/*.feature:10</comment>)"
            )
            ->configureProcessors()
            ->addOption('--strict', null, InputOption::VALUE_NONE,
                'Fail if there are any undefined or pending steps.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // if features argument provided
        if ($input->getArgument('features')) {
            // run specific bundle features
            return parent::execute($input, $output);
        }

        // otherways run all registered bundles features
        return $this->executeAllRegisteredBundles($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function executeAllRegisteredBundles(InputInterface $input, OutputInterface $output)
    {
        $gherkin = $this->getContainer()->get('gherkin');

        $testBundles   = (array) $this->getContainer()->getParameter('behat.bundles');
        $ignoreBundles = (array) $this->getContainer()->getParameter('behat.ignore_bundles');

        $this->startSuite();

        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if (count($testBundles) && !in_array($bundle->getName(), $testBundles)) {
                continue;
            }
            if (count($ignoreBundles) && in_array($bundle->getName(), $ignoreBundles)) {
                continue;
            }

            $contextClass = $bundle->getNamespace().'\Features\Context\FeatureContext';
            $featuresPath = $bundle->getPath().DIRECTORY_SEPARATOR.'Features';
            if (!class_exists($contextClass)) {
                continue;
            }

            // get all the needed services
            $pathsLocator   = $this->getContainer()->get('behat.path_locator');
            $definitionDisp = $this->getContainer()->get('behat.definition_dispatcher');
            $hookDisp       = $this->getContainer()->get('behat.hook_dispatcher');
            $contextDisp    = $this->getContainer()->get('behat.context_dispatcher');
            $contextReader  = $this->getContainer()->get('behat.context_reader');
            $logger         = $this->getContainer()->get('behat.logger');
            $parameters     = $contextDisp->getContextParameters();

            // load context information
            $contextDisp->setContextClass($contextClass);
            $contextReader->read();

            // locate bundle features
            $pathsLocator->locateBasePath($featuresPath);
            $paths = $pathsLocator->locateFeaturesPaths();

            // run bundle beforeSuite hooks
            $hookDisp->beforeSuite(new SuiteEvent($logger, $parameters, false));

            // read all features from their paths
            foreach ($paths as $path) {
                // parse every feature with Gherkin
                $features = $gherkin->load((string) $path);

                // and run it in FeatureTester
                foreach ($features as $feature) {
                    $feature->accept($this->getContainer()->get('behat.tester.feature'));
                }
            }

            // run bundle afterSuite hooks
            $hookDisp->afterSuite(new SuiteEvent($logger, $parameters, true));

            // clean definitions, transformations and hooks
            $definitionDisp->removeDefinitions();
            $definitionDisp->removeTransformations();
            $hookDisp->removeHooks();
        }

        return $this->finishSuite($input);
    }
}
