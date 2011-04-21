Provides Behat BDD support for your Symfony2 project.
See [Behat official site](http://behat.org) for more info.

## Features

- Support latest Symfony2 Standart Edition
- Fully integrates with Symfony2 project
- Uses `MinkBundle` to talk with browser emulators
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

### Add Gherkin, Behat & BehatBundle namespaces to autoload

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Behat\BehatBundle' => __DIR__.'/../vendor',
    'Behat\Behat'       => __DIR__.'/../vendor/Behat/Behat/src',
    'Behat\Gherkin'     => __DIR__.'/../vendor/Behat/Gherkin/src',
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

    // include PHPUnit assertions
    require_once 'PHPUnit/Autoload.php';
    require_once 'PHPUnit/Framework/Assert/Functions.php';
    ...
}
```

### Add behat configuration into your config

``` yml
# app/config/config_dev.yml
behat: ~
```

### Configuration parameters

BehatBundle supports almost all parameters, that `behat` itself support (exluding profiles and imports). Read [Behat configuration documentation](http://docs.behat.org/en/behat/configuration.html).

### Where to place features

Behat will search for next foleder structure (when running bundle features):

    path/to/your/BundleBundleBundle
    ├── Features
    │   ├── feature1.feature
    │   ├── feature2.feature
    │   ├── feature3.feature
    │   ├── support
    │   │   ├── bootstrap.php
    │   │   ├── env.php
    │   │   └── hooks.php
    │   └── steps
    │       └── your_steps.php
    ├── ...
    └── ...

Notice, that from Symfony2 PR12, BehatBundle stores features in `/path/to/BundleBundleBundle/Features`, not in `/path/to/BundleBundleBundle/Tests/Features`. This was done, because Behat features is more than simple tests and it should be used for bundle/project architect, not just for tests.

### Core steps

BehatBundle and MinkBundle comes bundled with core steps. Find all available steps with:

``` bash
app/console behat:test:bundle Your\\Bundle\\Namespace --steps
```

or even in your language (if MinkBundle steps support it):

``` bash
app/console behat:test:bundle Your\\Bundle\\Namespace --steps --lang fr
```

## Command line

BehatBundle provides some very useful CLI commands for running your features.

### Init bundle test suite structure

This command will create initial bundle features directory:

    php app/console behat:test:bundle --init Acme\\DemoBundle

### Run bundle tests

This command runs all features inside single bundle:

    php app/console behat:test:bundle Acme\\DemoBundle

### Run features by path

This command runs specified feature:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features/SingleFeature.feature

All features inside `src/Application/HelloBundle/Tests/Features` folder:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features

Single scenario on line 21 in specified feature:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features/SingleFeature.feature:21

### Options

BehatBundle supports almost all options, that Behat itself supports, including:

- `--format` or `-f`: switch formatter (default ones is *progress* & *pretty*)
- `--no-colors`: turn-off colors in formatter
- `--lang ...`: output formatter locale
- `--name ...`: filter features/scenarios by name
- `--tags ...`: filter features/scenarios by tag

## CREDITS

List of developers who contributed:

- Konstantin Kudryashov (ever.zet@gmail.com)
