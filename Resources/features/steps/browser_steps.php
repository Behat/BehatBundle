<?php

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$steps->Given('/^I am on(?: the)? (.*)$/', function($world, $page) {
    $world->crawler = $world->getClient()->request('GET', $world->pathTo($page));
});

$steps->When('/^I go to(?: the)? (.*)$/', function($world, $page) {
    $world->crawler = $world->getClient()->request('GET', $world->pathTo($page));
});

$steps->When('/^I (?:follow|click)(?: the)? "([^"]*)"(?: link)*$/', function($world, $link) {
    $link = $world->crawler->selectLink($link)->link();
    $world->crawler = $world->getClient()->click($link);
});

$steps->When('/^I go back$/', function($world) {
    $world->getClient()->back();
});

$steps->When('/^I go forward$/', function($world) {
    $world->getClient()->forward();
});

$steps->When('/^I send (POST|PUT|DELETE) to (.*) with:$/', function($world, $method, $page, $table) {
    $world->crawler = $world->getClient()->request($method, $world->pathTo($page), current($table->getHash()));
});

$steps->When('/^I follow redirect$/', function($world) {
    $world->crawler = $world->getClient()->followRedirect();
});
