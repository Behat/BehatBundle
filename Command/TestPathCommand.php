<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Command\BehatCommand;

/*
 * This file is part of the BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bundle Test Command.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class TestPathCommand extends BehatCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat:test:path')
            ->setDescription('Tests specified feature(s)')
            ->setDefinition(array_merge(
                array(
                    new InputArgument('features',
                        InputArgument::REQUIRED,
                        'The features path'
                    ),
                ),
                $this->getDemonstrationOptions(),
                $this->getFilterOptions(),
                $this->getFormatterOptions(),
                $this->getRunOptions()
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function createContainer($configFile = null, $profile = null)
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}
