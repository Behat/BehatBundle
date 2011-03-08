<?php

$steps->Given('/^basic BehatBundle environment$/', function($world) {
    $world->path = sys_get_temp_dir() . '/BehatBundle/features';
    $world->root = $world->getClient()->getKernel()->getContainer()->getParameter('kernel.root_dir');

    if (!is_dir($world->path)) {
        mkdir($world->path, 0777, true);
    }

    chdir($world->root);
});

$steps->Given('/^a feature named "([^"]*)" with:$/', function($world, $filename, $string) {
    file_put_contents($world->path . '/' . $filename, $string);
});

$steps->When('/^I run "([^"]*)"$/', function($world, $command) {
    $world->command = str_replace('%features_path%', $world->path, $command);

    // Execute command
    exec($world->command, $world->output, $world->return);
    $world->output = trim(implode("\n", $world->output));
    $world->output = str_replace(realpath(__DIR__ . '/../../../Resources') . '/', '', $world->output);
});

$steps->Then('/^Print last command output$/', function($world) {
    $world->printDebug("`" . $world->command . "`:\n" . $world->output);
});

$steps->Then('/^It should (fail|pass) with:$/', function($world, $success, $data) {
    if ('fail' === $success) {
        assertNotEquals(0, $world->return);
    } else {
        assertEquals(0, $world->return);
    }
    try {
        assertEquals((string) $data, $world->output);
    } catch (Exception $e) {
        $diff = PHPUnit_Framework_TestFailure::exceptionToString($e);
        throw new Exception($diff, $e->getCode(), $e);
    }
});

$steps->Then('/^It should (fail|pass)$/', function($world, $success) {
    if ('fail' === $success) {
        assertNotEquals(0, $world->return);
    } else {
        assertEquals(0, $world->return);
    }
});
