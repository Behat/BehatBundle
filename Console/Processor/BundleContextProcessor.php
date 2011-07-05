<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\ContextProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BundleContext processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class BundleContextProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        $contextDispatcher = $container->get('behat.context_dispatcher');
        $contextDispatcher->setContextClass($this->getContextClass($container, $input));

        $contextReader = $container->get('behat.context_reader');
        $contextReader->read();
    }

    /**
     * {@inheritdoc}
     */
    protected function getContextClass(ContainerInterface $container, InputInterface $input)
    {
        $locator           = $container->get('behat_bundle.namespace_locator');
        $namespace         = $locator->findNamespace($input->getArgument('namespace'));
        $namespacedContext = $namespace . '\Features\Context\FeatureContext';

        if (class_exists($namespacedContext)) {
            return $namespacedContext;
        }

        return $container->getParameter('behat.context.class');
    }
}
