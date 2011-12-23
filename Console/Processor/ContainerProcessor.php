<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Output\OutputInterface;
    
use Behat\Behat\Console\Processor\ContainerProcessor as BaseProcessor,
    Behat\BehatBundle\DependencyInjection\BehatExtension as BehatBundleExtension,
    Behat\Behat\DependencyInjection\BehatExtension,
    Behat\Behat\DependencyInjection\Compiler\GherkinPass,
    Behat\Behat\DependencyInjection\Compiler\ContextReaderPass,
    Behat\Behat\DependencyInjection\Compiler\EventDispatcherPass;
    
/*
 * This file is part of the Behat\BehatBundle.
 * (c)  Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Container processor.
 *
 * @author      Aurelien Fontaine <aurelien@efidev.com>
 */
class ContainerProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        
        $extension  = new BehatExtension();
        $cwd        = getcwd();
        $configFile = $input->getOption('config');
        $profile    = $input->getOption('profile') ?: 'default';

        if (null === $configFile) {
            if (is_file($cwd.DIRECTORY_SEPARATOR.'behat.yml')) {
                $configFile = $cwd.DIRECTORY_SEPARATOR.'behat.yml';
            } elseif (is_file($cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat.yml')) {
                $configFile = $cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat.yml';
            }
        }

        if (file_exists($configFile)) {
            $config = $extension->loadFromFile($configFile, $profile, $container);
        } else {
            $config = $extension->load(array(array()), $container);
        }

        $container->addCompilerPass(new GherkinPass());
        $container->addCompilerPass(new ContextReaderPass());
        $container->addCompilerPass(new EventDispatcherPass());

        if (file_exists($configFile)) {
            $container->get('behat.path_locator')->setPathConstant('BEHAT_CONFIG_PATH', dirname($configFile));
        }
        
        $extension  = new BehatBundleExtension();
        $cwd        = getcwd();
        $configFile = null;
        
        if (is_file($cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat_bundle.yml')) 
        {
              $configFile = $cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat_bundle.yml';
        } 
        elseif (is_file($cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat_bundle.xml')) 
        {
              $configFile = $cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'behat_bundle.xml';
        }

        
        
        if (file_exists($configFile)) {
            $config = $extension->loadFromFile($configFile, $profile, $container);
        } else {
            $config = $extension->load(array(array()), $container);
        }
        
        $container->compile();
    }

  
}
