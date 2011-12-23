<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder;

use Behat\Behat\DependencyInjection\BehatExtension,
    Behat\BehatBundle\DependencyInjection\BehatExtension as BehatBundleExtension,    
    Behat\Behat\Console\Command\BehatCommand as BaseCommand,
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
     * Service container.
     *
     * @var     Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        
        $this->container = new ContainerBuilder();
        
        $this
            ->setName('behat')
            ->setDescription('Tests Behat feature(s) in specified bundle')
            ->setProcessors(array(
                new BundleProcessor\ContainerProcessor(),
                new BundleProcessor\LocatorProcessor(),
                new BundleProcessor\InitProcessor(),
                new BundleProcessor\ContextProcessor(),
                new Processor\FormatProcessor(),
                new BundleProcessor\HelpProcessor(),
                new Processor\GherkinProcessor(),
                new Processor\RunProcessor(),
            ))
            ->addArgument('features', InputArgument::OPTIONAL,
                "Feature(s) to run. Could be:".
                "\n- a dir (<comment>src/to/Bundle/Features/</comment>), " .
                "\n- a feature (<comment>src/to/Bundle/Features/*.feature</comment>), " .
                "\n- a scenario at specific line (<comment>src/to/Bundle/Features/*.feature:10</comment>). " .
                "\n- Also, you can use short bundle notation (<comment>@BundleName/*.feature:10</comment>)"
            )
            ->configureProcessors()
        ;
        
        $kernel = new \AppKernel('test', 'true');
        $kernel->boot();
        $this->container->set('kernel',$kernel);
    }
    
    /**
     * {@inheritdoc} 
     */
    protected function getContainer() 
    {
        return $this->container;
    }
  
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        // if features argument provided
        if ($input->getArgument('features')) {
            // run specific bundle features
            return $this->getContainer()->get('behat.runner')->run();
        }
        
        // otherways run all registered bundles features
        return implode(PHP_EOL,$this->executeAllRegisteredBundles());
        
    }

    /**
     * {@inheritdoc}
     */
    protected function executeAllRegisteredBundles()
    {
        
        $testBundles   = (array) $this->container->getParameter('behat.bundles');
        $ignoreBundles = (array) $this->container->getParameter('behat.ignore_bundles');
    
        $output = array();
        
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
            $pathsLocator   = $this->getContainer()->get('behat.path_locator');

            // locate bundle features
            $pathsLocator->locateBasePath($featuresPath);
            
            $this->container->get('behat.runner')->setFeaturesPaths($pathsLocator->locateFeaturesPaths());

            $contextDispatcher = $this->container->get('behat.context_dispatcher');
            $contextDispatcher->setContextClass($contextClass);

            $contextReader = $this->container->get('behat.context_reader');
            $contextReader->read();
            
            $output[] = $this->container->get('behat.runner')->run();
        }

        return $output;
    }
}
