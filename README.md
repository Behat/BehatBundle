Provides Behat BDD support for your Symfony2 project.
See [Behat official site](http://behat.org) for more info.

## Features

- Support latest Symfony2 Standart Edition
- Fully integrates with Symfony2 project
- _Optionally_ uses `MinkBundle` to talk with browser emulators
- Fully tested with `BehatBundle` itself
- Covers basic functional testing needs

## Installation

### Add Behat\BehatBundle to your src dir.

If you're on PR# release:

``` bash
git submodule add -f git://github.com/Behat/BehatBundle.git vendor/Behat/BehatBundle
```

### Put Gherkin & Behat libs inside vendors folder

``` php
git submodule add -f git://github.com/Behat/Gherkin.git vendor/Behat/Gherkin
git submodule add -f git://github.com/Behat/Behat.git vendor/Behat/Behat
```
If you follow with deps:

``` bash
[BehatBundle]
    git=https://github.com/Behat/BehatBundle.git
    target=/bundles/Behat/BehatBundle

[Gherkin]
    git=https://github.com/Behat/Gherkin.git
    target=/behat/Gherkin

[Behat]
    git=https://github.com/Behat/Behat.git
    target=/behat/Behat
```

### Add Gherkin, Behat & BehatBundle namespaces to autoload

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Behat\BehatBundle' => __DIR__.'/../vendor',
    'Behat\Behat'       => __DIR__.'/../vendor/behat/Behat/src',
    'Behat\Gherkin'     => __DIR__.'/../vendor/behat/Gherkin/src',
    // ...
));
```

### Add BehatBundle into your application kernel

``` php
<?php
// app/AppKernel.php
if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    ...
    $bundles[] = new Behat\BehatBundle\BehatBundle();
    ...
}
```

### Add behat configuration into your config

``` yml
# app/config/config_dev.yml
behat: ~
```

## Init bundle features suite

Create your bundle and run:

``` bash
app/console behat:test:bundle --init Acme\\YourBundle
```

this will create next structure:

    path/to/Acme/YourBundle
    ├── Features
    │   ├── feature1.feature
    │   ├── feature2.feature
    │   ├── feature3.feature
    │   └── Context
    │       └── FeatureContext.php
    ├── ...
    └── ...

If you look closely at `path/to/Acme/YourBundle/Features/Context/FeatureContext.php`, you'll see, that it extends base `BehatContext`, which comes with `BehatBundle` and just gives you ability to get applications kernel or container.

If you want to test web interface of your application with Mink:

1. Install MinkBundle and Mink as [described here](https://github.com/Behat/MinkBundle#readme).
2. Extend `MinkContext` instead of basic `BehatContext` in your `FeatureContext` class.

## Check available step definitions

If you extended your `FeatureContext` from `MinkContext`, then you could use one of the predefined web steps. You can check all available for specific context (bundle) definitions with:

``` bash
app/console behat Your\\Bundle\\Namespace --definitions
```

or even in your language:

``` bash
app/console behat Your\\Bundle\\Namespace --definitions --lang fr
```

## Command line

BehatBundle provides some very useful CLI commands for running your features.

### Init bundle test suite structure

This command will create initial bundle features directory:

    php app/console behat --init Acme\\DemoBundle

### Run bundle tests

This command runs all features inside single bundle:

    php app/console behat Acme\\DemoBundle

### Run features by path

This command runs specified feature:

    php app/console behat src/Application/HelloBundle/Tests/Features/SingleFeature.feature

All features inside `src/Application/HelloBundle/Tests/Features` folder:

    php app/console behat src/Application/HelloBundle/Tests/Features

Single scenario on line 21 in specified feature:

    php app/console behat src/Application/HelloBundle/Tests/Features/SingleFeature.feature:21

## CREDITS

List of developers who contributed:

- Konstantin Kudryashov (ever.zet@gmail.com)
