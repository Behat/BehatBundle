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

        $container->get('behat.runner')->setMainContextClass(
            $this->getContextClass($container, $input)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContextClass(ContainerInterface $container, InputInterface $input)
    {
        $featuresPath = preg_replace('/\:\d+$/', '', $input->getArgument('features'));
        if (preg_match('/^\@([^\/\\\\]+)(.*)$/', $featuresPath, $matches)) {
            $bundleNamespace = $container->get('kernel')->getBundle($matches[1])->getNamespace();
        } else {
            foreach ($container->get('kernel')->getBundles() as $bundle) {
                if (false !== strpos(realpath($featuresPath), realpath($bundle->getPath()))) {
                    $bundleNamespace = $bundle->getNamespace();
                    break;
                }
            }
        }

        return $container->get('behat.runner')->getContextClassForBundle($bundleNamespace);
    }
}
