Provides Behat BDD support for your Symfony2 project.
See [Behat official site](http://behat.org) for more info.

## Features

- Support latest Symfony2 release (PR6)
- Fully integrates with Symfony2 project
- Fully tested with Behat itself
- Covers basic functional testing needs
- Beautifull bundle testing possibilities

## Installation

### Add Behat\BehatBundle to your src dir.

If you're on PR# release:

    git submodule add git://github.com/Behat/BehatBundle.git src/Behat/BehatBundle

If you're on fabpot's master:

    git submodule add git://github.com/Behat/BehatBundle.git src/Behat/BehatBundle
    cd src/Behat/BehatBundle
    git checkout trunk
    cd ../../../

### Put Gherkin & Behat libs inside vendors folder

    git submodule add git://github.com/Behat/Gherkin vendor/Gherkin
    git submodule add git://github.com/Behat/Behat vendor/Behat

### Add Gherkin, Behat & BehatBundle namespaces to autoload

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Behat\\Gherkin'        => __DIR__.'/../vendor/Gherkin/src',
        'Behat\\Behat'          => __DIR__.'/../vendor/Behat/src',
        'Behat\\BehatBundle'    => __DIR__.'/../src',
        // ...
    ));

### Add BehatBundle into your application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Behat\BehatBundle\BehatBundle(),
            // ...
        );
    }

### Add behat configuration into your config

    # app/config/config.yml
    behat.config: ~

### Configuration parameters

BehatBundle have configuration alias:

- `behat.config` is core configurator of BehatBundle. Specify default formatter parameters and output options here.

For example, by default Behat uses *pretty* formatter. If you want to always use *progress* formatter instead of
specifying `-f ...` option everytime, add this to your config:

    # app/config/config.yml
    behat.config:
      format:
        name:   progress

## Write features

Put your features inside your `BundleName/Tests/Features/` directory, steps inside `BundleName/Tests/Features/steps`.
`hooks.php`, `bootstrap.php` and `env.php` inside `Bundle/Tests/Features/support`.

### Core steps

BehatBundle comes bundled with core steps. Look at them inside Bundle's `Behat/BehatBundle/Resources/features` folder. Also, you can view how to use them by looking at `Behat/BehatBundle/Tests/Features/*` core BehatBundle tests.

## Command line

BehatBundle provides some very useful CLI commands for running your features.

### Run bundle tests

This command runs all features inside single bundle:

    php app/console behat:test:bundle Application\\HelloBundle

to run HelloBundle application tests. But don't forget to configure routing to include test routes.

### Run features by path

This command runs specified feature:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features/SingleFeature.feature

All features inside `src/Application/HelloBundle/Tests/Features` folder:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features

Single scenario on line 21 in specified feature:

    php app/console behat:test:path src/Application/HelloBundle/Tests/Features/SingleFeature.feature:21

### Options

BehatBundle supports all options, that Behat itself supports, including:

- `--format` or `-f`: switch formatter (default ones is *progress* & *pretty*)
- `--no-colors`: turn-off colors in formatter
- `--lang ...`: output formatter locale
- `--tags ...`: filter features/scenarios by tag

## CREDITS

List of developers who contributed:

- Konstantin Kudryashov (ever.zet@gmail.com)
