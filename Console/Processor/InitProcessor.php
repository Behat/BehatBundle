<?php

namespace Behat\BehatBundle\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Input\InputInterface,
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
        // throw exception if no features argument provided
        if (!$input->getArgument('features') && $input->getOption('init')) {
            throw new \InvalidArgumentException('Provide features argument in order to init suite');
        }

        if ($input->getOption('init')) {
            $this->initBundleDirectoryStructure($container, $input, $output);

            exit(0);
        }
    }

    /**
     * Inits bundle directory structure
     *
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container
     * @param   Symfony\Component\Console\Input\InputInterface              $input
     * @param   Symfony\Component\Console\Output\OutputInterface            $output
     */
    protected function initBundleDirectoryStructure(ContainerInterface $container, InputInterface $input, OutputInterface $output)
    {
        $bundlePath  = preg_replace('/Bundle[\/\\\\]Features.*$/', 'Bundle', $input->getArgument('features'));
        $bundleFound = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            if (realpath($bundle->getPath()) === realpath($bundlePath)) {
                $bundleFound = $bundle;
                break;
            }
        }
        if (null === $bundleFound) {
            throw new \InvalidArgumentException(
                sprintf('Can not find bundle at path "%s". Have you enabled it?', $bundlePath)
            );
        }

        $featuresPath = $bundlePath.DIRECTORY_SEPARATOR.'Features';
        $locator      = $container->get('behat.path_locator');
        $basePath     = realpath($locator->getWorkPath()).DIRECTORY_SEPARATOR;
        $contextPath  = $featuresPath.DIRECTORY_SEPARATOR.'Context';
        $namespace    = $bundleFound->getNamespace();

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
    Behat\Behat\Exception\PendingException;
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
