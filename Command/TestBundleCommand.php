<?php

namespace Behat\BehatBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Finder\Finder;

use Behat\Behat\Console\Command\BehatCommand,
    Behat\Behat\PathLocator;

/*
 * This file is part of the Behat\BehatBundle.
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
class TestBundleCommand extends BehatCommand
{
    private $bundlePath;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('behat:test:bundle')
            ->setDescription('Tests specified bundle features')
            ->setDefinition(array_merge(
                array(
                    new InputArgument('namespace',
                        InputArgument::REQUIRED,
                        'The bundle namespace'
                    ),
                ),
                $this->getInitOptions(),
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

    /**
     * {@inheritdoc}
     */
    protected function locateBasePath(PathLocator $locator, InputInterface $input)
    {
        $bundlePath = $this->locateBundlePath($input, $this->createContainer());

        return $locator->locateBasePath($bundlePath . DIRECTORY_SEPARATOR . 'Features');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContextClass(InputInterface $input, ContainerInterface $container)
    {
        if (!preg_match('/Bundle$/', $namespace = $input->getArgument('namespace'))) {
            throw new \InvalidArgumentException('The namespace must end with Bundle.');
        }

        $namespacedContext = $namespace . '\Features\Context\FeatureContext';
        if (class_exists($namespacedContext)) {
            return $namespacedContext;
        }

        return $container->getParameter('behat.context.class');
    }

    /**
     * Locate current bundle path.
     *
     * @param   Symfony\Component\Console\Input\InputInterface              $input      input instance
     * @param   Symfony\Component\DependencyInjection\ContainerInterface    $container  service container
     *
     * @return  string
     */
    protected function locateBundlePath(InputInterface $input, ContainerInterface $container)
    {
        if (!preg_match('/Bundle$/', $namespace = $input->getArgument('namespace'))) {
            throw new \InvalidArgumentException('The namespace must end with Bundle.');
        }

        $bundlePath = null;
        foreach ($container->get('kernel')->getBundles() as $bundle) {
            $tmp = str_replace('\\', '/', get_class($bundle));
            $bundleNamespace = str_replace('/', '\\', dirname($tmp));
            if ($namespace === $bundleNamespace) {
                $bundlePath = realpath($bundle->getPath());
                break;
            }
        }

        if (null === $bundlePath) {
            throw new \InvalidArgumentException(
                sprintf("Unable to test bundle (%s is not a defined namespace).", $namespace)
            );
        }

        return $bundlePath;
    }

    /**
     * {@inheritdoc}
     */
    protected function initFeaturesDirectoryStructure(PathLocator $locator, OutputInterface $output)
    {
        $basePath     = realpath($locator->getWorkPath()) . DIRECTORY_SEPARATOR;
        $featuresPath = $locator->getFeaturesPath();
        $contextPath  = $featuresPath . DIRECTORY_SEPARATOR . 'Context';

        $namespace  = '';
        $bundlePath = dirname($featuresPath);
        $container  = $this->getApplication()->getKernel()->getContainer();
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
//        $contianer = $this->getContainer();
//        $container->get('some_service')->doSomethingWith($argument);
//    }
//
}

PHP;
    }
}
