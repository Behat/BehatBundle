<?php

/*
 * This file is part of the BehatBundle.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$steps->Then('/^Request method is (.*)$/', function($world, $method) {
    assertEquals($method, $world->getClient()->getRequest()->getMethod());
});

$steps->Then('/^Request has cookie "([^"]*)"$/', function($world, $cookie) {
    assertTrue($world->getClient()->getRequest()->cookies->has($cookie));
});

$steps->Then('/^Request has not cookie "([^"]*)"$/', function($world, $cookie) {
    assertFalse($world->getClient()->getRequest()->cookies->has($cookie));
});

$steps->Then('/^Request cookie "([^"]*)" is "([^"]*)"$/', function($world, $cookie, $val) {
    assertEquals($val, $world->getClient()->getRequest()->cookies->get($cookie));
});
