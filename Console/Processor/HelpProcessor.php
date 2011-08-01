<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\HelpProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Help processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class HelpProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        // throw exception on --definitions if no features argument provided
        if (!$input->getArgument('features') && $input->getOption('definitions')) {
            throw new \InvalidArgumentException('Provide features argument in order to print definitions');
        }

        parent::process($container, $input, $output);
    }
}
