<?php

namespace Behat\BehatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use Behat\Behat\DependencyInjection\Configuration as BaseConfiguration;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BehatBundle configuration.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Configuration extends BaseConfiguration
{
    /**
     * {@inheritdoc}
     */
    protected function appendConfigChildrens(TreeBuilder $tree)
    {
        return parent::appendConfigChildrens($tree)->
            fixXmlConfig('bundle')->
            children()->
                arrayNode('bundles')->
                    prototype('scalar')->
                    end()->
                end()->
            end()->
            fixXmlConfig('ignore-bundle')->
            children()->
                arrayNode('ignore_bundles')->
                    defaultValue(array('BehatBundle'))->
                    prototype('scalar')->
                    end()->
                end()->
            end();
    }
}
