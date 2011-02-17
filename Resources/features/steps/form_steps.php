<?php

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$steps->When('/^I fill in "([^"]*)" with "([^"]*)"$/', function($world, $field, $value) {
    $world->inputFields[$field] = $value;
});

$steps->When('/^I select "([^"]*)" from "([^"]*)"$/', function($world, $value, $field) {
    $world->inputFields[$field] = $value;
});

$steps->When('/^I check "([^"]*)"$/', function($world, $field) {
    $world->inputFields[$field] = true;
});

$steps->When('/^I uncheck "([^"]*)"$/', function($world, $field) {
    $world->inputFields[$field] = false;
});

$steps->When('/^I attach the file at "([^"]*)" to "([^"]*)"$/', function($world, $path, $field) {
    $world->inputFields[$field] = $path;
});

$steps->When('/^I press "([^"]*)" in (.*) form$/', function($world, $button, $formName) {
    $form = $world->crawler->selectButton($button)->form();
    $world->crawler = $world->getClient()->submit($form, $world->inputFields);
    $world->inputFields = array();
});
