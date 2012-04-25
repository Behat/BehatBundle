<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\LocatorProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Locator processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class LocatorProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        // ignore location if no features argument provided
        if (!$input->getArgument('features')) {
            return;
        }

        if (preg_match('/^\@([^\/\\\\]+)(.*)$/', $input->getArgument('features'), $matches)) {
            $bundle = $container->get('kernel')->getBundle($matches[1]);

            $prefix = $container->getParameter('behat.namespace.prefix') ? DIRECTORY_SEPARATOR.trim($container->getParameter('behat.namespace.prefix'), '\\')  : null;
            $input->setArgument(
                'features', realpath($bundle->getPath()).$prefix.DIRECTORY_SEPARATOR.'Features'.$matches[2]
            );
        }

        parent::process($container, $input, $output);
    }
}
