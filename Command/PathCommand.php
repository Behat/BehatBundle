<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

use Behat\Behat\Console\Processor;

use Behat\BehatBundle\Console\Processor as BundleProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Path testing command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PathCommand extends BundleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setProcessors(array(
            new Processor\LocatorProcessor(),
            new BundleProcessor\PathContextProcessor(),
            new Processor\FormatProcessor(),
            new Processor\HelpProcessor(),
            new Processor\GherkinProcessor(),
            new Processor\RerunProcessor(),
        ));

        $this
            ->setName('behat:path')
            ->setDescription('Tests specified feature(s)')
            ->setDefinition(array_merge(
                array(
                    new InputArgument('features',
                        InputArgument::REQUIRED,
                        'The features path'
                    ),
                ),
                $this->getProcessorsInputOptions(),
                array(
                    new InputOption('--strict',         null,
                        InputOption::VALUE_NONE,
                        '       ' .
                        'Fail if there are any undefined or pending steps.'
                    )
                )
            ))
        ;
    }
}
