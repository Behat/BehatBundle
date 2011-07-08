<?php

namespace Behat\BehatBundle\Console\Processor\Path;

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
 * Path locator processor.
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
        if (preg_match('/^\@([^\/\\\\]+)(.*)$/', $input->getArgument('features'), $matches)) {
            $bundle = $container->get('kernel')->getBundle($matches[1]);
            $input->setArgument(
                'features', realpath($bundle->getPath()).DIRECTORY_SEPARATOR.'Features'.$matches[2]
            );
        }

        parent::process($container, $input, $output);
    }
}
