<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Output\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\PathLocator,
    Behat\Behat\Console\Processor\InitProcessor as BaseProcessor;

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Init operation processor.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class InitProcessor extends BaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('init')) {
            $this->initBundleDirectoryStructure($container, $output);

            exit(0);
        }
    }

    /**
     * Inits bundle directory structure
     *
     * @param   Symfony\Component\DependencyInjection\ContainerInterface  $container
     * @param   Symfony\Component\Console\Output\OutputInterface
     */
    protected function initBundleDirectoryStructure(ContainerInterface $container, OutputInterface $output)
    {
        $locator      = $container->get('behat.path_locator');
        $basePath     = realpath($locator->getWorkPath()).DIRECTORY_SEPARATOR;
        $featuresPath = $locator->getFeaturesPath();
        $contextPath  = $featuresPath.DIRECTORY_SEPARATOR.'Context';

        $namespace  = '';
        $bundlePath = dirname($featuresPath);
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            if (false !== strpos($bundlePath, $bundle->getPath())) {
                $tmp = str_replace('\\', '/', get_class($bundle));
                $namespace = str_replace('/', '\\', dirname($tmp));
                break;
            }
        }

        if (!is_dir($featuresPath)) {
            mkdir($featuresPath, 0777, true);
            $output->writeln(
                '<info>+d</info> ' .
                str_replace($basePath, '', realpath($featuresPath)) .
                ' <comment>- place your *.feature files here</comment>'
            );
        }

        if (!is_dir($contextPath)) {
            mkdir($contextPath, 0777, true);

            file_put_contents(
                $contextPath . DIRECTORY_SEPARATOR . 'FeatureContext.php',
                strtr($this->getFeatureContextSkelet(), array(
                    '%NAMESPACE%' => $namespace
                ))
            );

            $output->writeln(
                '<info>+f</info> ' .
                str_replace($basePath, '', realpath($contextPath)) . DIRECTORY_SEPARATOR .
                'FeatureContext.php <comment>- place your feature related code here</comment>'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFeatureContextSkelet()
    {
return <<<'PHP'
<?php

namespace %NAMESPACE%\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
    Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\Pending;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends BehatContext //MinkContext if you want to test web
{
//
// Place your definition and hook methods here:
//
//    /**
//     * @Given /^I have done something with "([^"]*)"$/
//     */
//    public function iHaveDoneSomethingWith($argument)
//    {
//        $container = $this->getContainer();
//        $container->get('some_service')->doSomethingWith($argument);
//    }
//
}

PHP;
    }
}
