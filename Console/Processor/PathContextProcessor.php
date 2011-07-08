<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PathContext processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PathContextProcessor extends BundleContextProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function getContextClass(ContainerInterface $container, InputInterface $input)
    {
        $featuresPath = $input->getArgument('features');
        if (preg_match('/^(.*)\:\d+$/', $featuresPath, $matches)) {
            $featuresPath = $matches[1];
        }

        $namespacedContext = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            if (false !== strpos(realpath($featuresPath), realpath($bundle->getPath()))) {
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
