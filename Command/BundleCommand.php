<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

use Behat\Behat\Console\Command\BehatCommand,
    Behat\Behat\Console\Processor;

use Behat\BehatBundle\Console\Processor as BundleProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bundle testing command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class BundleCommand extends BehatCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat:bundle')
            ->setDescription('Tests specified bundle features')
            ->setProcessors(array(
                new BundleProcessor\LocatorProcessor(),
                new BundleProcessor\InitProcessor(),
                new BundleProcessor\BundleContextProcessor(),
                new Processor\FormatProcessor(),
                new Processor\HelpProcessor(),
                new Processor\GherkinProcessor(),
                new Processor\RerunProcessor(),
            ))
            ->addArgument('namespace', InputArgument::REQUIRED,
                'The bundle namespace. Could be a full namespace (<comment>Acme\\DemoBundle</comment>), ' .
                'reversed namespace (<comment>Acme/DemoBundle</comment>) or just a bundle name ' .
                '(<comment>AcmeDemoBundle</comment>).'
            )
            ->configureProcessors()
            ->addOption('--strict', null, InputOption::VALUE_NONE,
                'Fail if there are any undefined or pending steps.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}
