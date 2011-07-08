<?php

namespace Behat\BehatBundle\Console\Processor\Path;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface;

use Behat\BehatBundle\Console\Processor\Bundle\ContextProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Path Context processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ContextProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function getContextClass(ContainerInterface $container, InputInterface $input)
    {
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

        return $container->getParameter('behat.context.class');
    }
}
