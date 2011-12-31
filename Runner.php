<?php

namespace Behat\BehatBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Behat\Behat\Event\SuiteEvent;
use Behat\Behat\Runner as BaseRunner;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat suite runner.
 *
 * @author      Christophe Coevoet <stof@notk.org>
 */
class Runner extends BaseRunner
{
    private $container;
    private $runAllBundles = false;

    /**
     * Initializes runner.
     *
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * Sets runner to run all bundles.
     *
     * @param   Boolean $runAll
     */
    public function setRunAllBundles($runAll = true)
    {
        $this->runAllBundles = (bool) $runAll;
    }

    /**
     * Runs feature suite.
     *
     * @return  integer CLI return code
     */
    public function run()
    {
        if (!$this->runAllBundles) {
            return parent::run();
        }

        return $this->runAllRegisteredBundles();
    }

    protected function runAllRegisteredBundles()
    {
        $gherkin = $this->container->get('gherkin');

        $testBundles   = (array) $this->container->getParameter('behat.bundles');
        $ignoreBundles = (array) $this->container->getParameter('behat.ignore_bundles');

        $this->beforeSuite();

        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
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
            $pathsLocator   = $this->container->get('behat.path_locator');
            $definitionDisp = $this->container->get('behat.definition_dispatcher');
            $hookDisp       = $this->container->get('behat.hook_dispatcher');
            $contextDisp    = $this->container->get('behat.context_dispatcher');
            $contextReader  = $this->container->get('behat.context_reader');
            $logger         = $this->container->get('behat.logger');
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
                    $tester = $this->container->get('behat.tester.feature');
                    $tester->setDryRun($this->isDryRun());

                    $feature->accept($tester);
                }
            }

            // run bundle afterSuite hooks
            $hookDisp->afterSuite(new SuiteEvent($logger, $parameters, true));

            // clean definitions, transformations and hooks
            $definitionDisp->removeDefinitions();
            $definitionDisp->removeTransformations();
            $hookDisp->removeHooks();
        }

        $this->afterSuite();

        return $this->getCliReturnCode();
    }
}
