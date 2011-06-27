<?php

namespace Behat\BehatBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/*
 * This file is part of the Behat\BehatBundle.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BehatBundle step definitions context.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FeatureContext extends BehatContext
{
    private $path;
    private $root;
    private $command;
    private $output;
    private $return;

    /**
     * @Given /^basic BehatBundle environment$/
     */
    public function basicBehatEnvironment()
    {
        $this->path = sys_get_temp_dir() . '/BehatBundle/features';
        $this->root = $this->getKernel()->getContainer()->getParameter('kernel.root_dir');

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }

        if (!is_dir($this->path . '/bootstrap')) {
            mkdir($this->path . '/bootstrap', 0777, true);
        }

        file_put_contents($this->path . '/bootstrap/FeatureContext.php',
            '<?php class FeatureContext extends Behat\BehatBundle\Context\MinkContext {}'
        );

        chdir($this->root);
    }

    /**
     * @Given /^a feature named "([^"]*)" with:$/
     */
    public function aFeatureNamedWith($filename, PyStringNode $string)
    {
        file_put_contents($this->path . '/' . $filename, $string);
    }

    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($command)
    {
        $this->command = str_replace('%features_path%', $this->path, $command);

        // Execute command
        exec($this->command . ' --env test', $this->output, $this->return);
        $this->output = trim(implode("\n", $this->output));
    }

    /**
     * @Then /^It should (fail|pass) with:$/
     */
    public function assertFailOrPassWith($success, PyStringNode $data)
    {
        if ('fail' === $success) {
            assertNotEquals(0, $this->return);
        } else {
            assertEquals(0, $this->return);
        }
        $realData = preg_replace('/\# \/.*BehatBundle\//', '# ', (string) $this->output);

        try {
            assertEquals((string) $data, $realData);
        } catch (\Exception $e) {
            $diff = \PHPUnit_Framework_TestFailure::exceptionToString($e);
            throw new \Exception($diff, $e->getCode(), $e);
        }
    }

    /**
     * @Then /^It should (fail|pass)$/
     */
    public function assertFailOrPass($success)
    {
        if ('fail' === $success) {
            assertNotEquals(0, $this->return);
        } else {
            assertEquals(0, $this->return);
        }
    }

    /**
     * @Then /^Print last command output$/
     */
    public function printLastCommandOutput()
    {
        $this->printDebug("`" . $this->command . "`:\n" . $this->output);
    }
}
