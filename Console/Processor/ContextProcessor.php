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
 * Context processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ContextProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        // ignore context initialization if no features argument provided
        if (!$input->getArgument('features')) {
            return;
        }

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
        $contextClass = $container->getParameter('behat.context.class');
        if (class_exists($contextClass)) {
            return $contextClass;
        }

        $featuresPath = preg_replace('/\:\d+$/', '', $input->getArgument('features'));

        $namespacedContext = null;
        if (preg_match('/^\@([^\/\\\\]+)(.*)$/', $featuresPath, $matches)) {
            $bundle = $container->get('kernel')->getBundle($matches[1]);
            $namespacedContext = $bundle->getNamespace() . '\Features\Context\FeatureContext';
        } else {
            foreach ($container->get('kernel')->getBundles() as $bundle) {
                if (false !== strpos(realpath($featuresPath), realpath($bundle->getPath()))) {
                    $namespacedContext = $bundle->getNamespace() . '\Features\Context\FeatureContext';
                    break;
                }
            }
        }

        if (null !== $namespacedContext && class_exists($namespacedContext)) {
            return $namespacedContext;
        }

        throw new \RuntimeException('Behat context class not found');
    }
}
