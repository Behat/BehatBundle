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
    private $runAllBundles = false;

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
     * {@inheritdoc}
     */
    public function getContextClassForBundle($bundleNamespace)
    {
        $contextClass = $this->getContainer()->getParameter('behat.context.class');

        if (null !== $contextClass && class_exists($bundleNamespace.'\\'.$contextClass)) {
            return $bundleNamespace.'\\'.$contextClass;
        }

        if (null !== $contextClass && class_exists($contextClass)) {
            return $contextClass;
        }

        if (class_exists($bundleNamespace.'\\Features\\Context\\FeatureContext')) {
            return $bundleNamespace.'\\Features\\Context\\FeatureContext';
        }


        $prefix = $this->getContainer()->getParameter('behat.namespace.prefix') ? '\\'.trim($this->getContainer()->getParameter('behat.namespace.prefix'), '\\') : null ;
        if (class_exists($bundleNamespace.$prefix.'\\Features\\Context\\FeatureContext')) {
            return $bundleNamespace.$prefix.'\\Features\\Context\\FeatureContext';
        }
    }

    /**
     * Runs feature suite.
     *
     * @return  integer CLI return code
     */
    public function runSuite()
    {
        if (!$this->runAllBundles) {
            return parent::runSuite();
        }

        $gherkin       = $this->getContainer()->get('gherkin');
        $logger        = $this->getContainer()->get('behat.logger');
        $hooks         = $this->getContainer()->get('behat.hook_dispatcher');
        $parameters    = $this->getContainer()->get('behat.context_dispatcher')->getContextParameters();
        $testBundles   = (array) $this->getContainer()->getParameter('behat.bundles');
        $ignoreBundles = (array) $this->getContainer()->getParameter('behat.ignore_bundles');

        $this->beforeSuite();

        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if (count($testBundles) && !in_array($bundle->getName(), $testBundles)) {
                continue;
            }
            if (count($ignoreBundles) && in_array($bundle->getName(), $ignoreBundles)) {
                continue;
            }

            $contextClass = $this->getContextClassForBundle($bundle->getNamespace());
            if (!class_exists($contextClass)) {
                continue;
            }
            $featuresPath = $bundle->getPath().DIRECTORY_SEPARATOR.'Features';

            $this->setMainContextClass($contextClass);
            $this->setLocatorBasePath($featuresPath);

            $hooks->beforeSuite(new SuiteEvent($logger, $parameters, false));
            $this->runFeatures($gherkin, $this->getFeaturesPaths());
            $hooks->afterSuite(new SuiteEvent($logger, $parameters, true));

            $this->cleanContextInformation();
        }

        $this->afterSuite();

        return $this->getCliReturnCode();
    }
}
