BehatBundle for Symfony2
========================

BehatBundle teaches `Behat <http://behat.org>`_ everything about your `Symfony2
<http://symfony.com>`_ application.

Behat Installation
------------------

In order to be able to use BehatBundle, you need to install Behat first.

Method #1 (PEAR)
~~~~~~~~~~~~~~~~

The simplest way to install Behat is through PEAR:

.. code-block:: bash

    $ pear channel-discover pear.behat.org
    $ pear install behat/behat

Now you should include Behat's autoloader in your ``AppKernel::registerBundles()``
method (for ``test`` environment only):

.. code-block:: php

    <?php # app/AppKernel.php
    
    //...

    if ('test' === $this->getEnvironment()) {
        // don't autoload Symfony2 classes, as they are
        // already loaded by the Symfony2 itself
        if (!defined('BEHAT_AUTOLOAD_SF2')) define('BEHAT_AUTOLOAD_SF2', false);
        require_once 'behat/autoload.php';
    }

    //...

Method #2 (Git)
~~~~~~~~~~~~~~~

Add next lines to your ``deps`` file:

.. code-block:: ini

    [gherkin]
        git=https://github.com/Behat/Gherkin.git
        target=/behat/gherkin

    [behat]
        git=https://github.com/Behat/Behat.git
        target=/behat/behat

Now run:

.. code-block:: bash

    $ bin/vendors install

in order to install all missing parts.

It's time to setup your ``app/autoload.php``:

.. code-block:: php

    $loader->registerNamespaces(array(
    //...
        'Behat\Gherkin' => __DIR__.'/../vendor/behat/gherkin/src',
        'Behat\Behat'   => __DIR__.'/../vendor/behat/behat/src',
    //...
    ));

Bundle Installation & Setup
---------------------------

Now it's time to install and setup ``BehatBundle`` itself.

1. Add ``BehatBundle`` repository address to your ``deps`` file:

    .. code-block:: ini

        [BehatBundle]
            git=https://github.com/Behat/BehatBundle.git
            target=/bundles/Behat/BehatBundle

2. Add  it to ``app/autoload.php``:

    .. code-block:: php

        $loader->registerNamespaces(array(
        //...
            'Behat\BehatBundle' => __DIR__.'/../vendor/bundles',
        //...
        ));

3. And enable it in your app kernel (``app/AppKernel.php``):

    .. code-block:: php

        if ('test' === $this->getEnvironment()) {
            $bundles[] = new Behat\BehatBundle\BehatBundle();
        }

4. Run ``bin/vendors install`` once again:

.. code-block:: bash

    $ bin/vendors install

Prepare Your Bundle for Behat
-----------------------------

In order for Behat to be able to test your bundle, you need to do two things:

1. Create ``Features`` folder in your bundle directory

2. Create custom ``FeatureContext`` class in your bundle's ``Features`` folder

BehatBundle can do this for you with one simple command:

.. code-block:: bash

    $ app/console -e=test behat --init @AcmeDemoBundle

It will init features suite structure in your bundle and create basic
``FeatureContext`` for you.

.. note::

    Notice, that we've enabled BehatBundle in ``test`` environment only, but
    ``app/console`` works in ``dev`` environment by default! That's why we
    specify ``-e=test`` option at the begining of ``app/console`` call.

.. tip::

    BehatBundle can accept full bundle path (``src/Acme/DemoBundle``) in
    addition to full bundle name (``@AcmeDemoBundle``) - you can use either
    one, that fits your flow best.

In contrast with Behat itself, which runs suite in context of whole application,
BehatBundle runs bundle-oriented suites. Everything in Symfony2 is a bundle and
every bundle in your application could have feature suite. BehatBundle makes it
extremely easy to test them with Behat.

Bundle's ``FeatureContext`` Class
---------------------------------

Every bundle in your application could have its own feature suite. And every
feature suite has its own independent context class (``FeatureContext``).

For example, created in previous chapter context class would look like this:

.. code-block:: php

    <?php # src/Acme/DemoBundle/Features/Context/FeatureContext.php

    namespace Acme\DemoBundle\Features\Context;

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

It's your own class from now on. You can require other libraries, describe 
bundle step definitions and hooks here. Feel like home ;-)

As this doc intro states: "BehatBundle teaches Behat everything about your
Symfony2 app". But how you can use all these knowledge in your contexts?
``Behat\BehatBundle\Context\BehatContext`` and ``Behat\BehatBundle\Context\MinkContext``
from which ones you should inherit your own contexts, provide set of useful
methods, that you could use in your suite definitions or hooks:

.. code-block:: php

    /**
     * @Given /^I have done something with "([^"]*)"$/
     */
    public function iHaveDoneSomethingWith($argument)
    {
        // get your app service container:
        $container = $this->getContainer();
        $container->get('some_service')->doSomethingWith($argument);

        // get your app kernel:
        $kernel = $this->getKernel();
        $bundles = $kernel->getBundles();
    }

.. note::

    In contrast with Behat's contexts, that get array of context parameters as
    context constructor argument - BehatBundle contexts **always** get
    KernelInterface instance as argument:

    .. code-block:: php

        <?php

        namespace Acme\DemoBundle\Features\Context;

        use Behat\BehatBundle\Context\BehatContext;
        use Symfony\Component\HttpKernel\KernelInterface;

        class FeatureContext extends BehatContext
        {
            public function __construct(KernelInterface $kernel)
            {
                // ...
            }
        }

.. note::

    BehatBundle provides two new contexts:

    1. ``Behat\BehatBundle\Context\BehatContext``
    2. ``Behat\BehatBundle\Context\MinkContext``

    They both are just a extension layer on top of the:

    1. ``Behat\Behat\Context\BehatContext``
    2. ``Behat\Mink\Behat\Context\MinkContext``

    which adds Symfony2 application knowledge to them (kernel, service
    container, services and their parameters). So, it's a preferred way to
    extend and use BehatBundle contexts instead of basic Behat and Mink ones.

Mink Integration: ``MinkContext``
---------------------------------

By default, BehatBundle creates ``FeatureContext``, that inherit from simplest
``Behat\BehatBundle\Context\BehatContext`` class. But if you want to test your
app with `Mink <http://mink.behat.org>`_ web acceptance testing framework - you
should extend ``Behat\BehatBundle\Context\MinkContext`` instead.

.. note::

    In order to be able to use ``MinkContext``, you should install and configure
    `MinkBundle <http://mink.behat.org/bundle>`_ first.

After changing base class for your bundle context from
``Behat\BehatBundle\Context\BehatContext`` to ``Behat\BehatBundle\Context\MinkContext``
you'll be able to use out-of-the box Mink steps in your bundle features. To
check all available steps, run:

.. code-block:: bash

    $ app/console -e=test behat @AcmeDemoBundle --definitions

or even for specific language:

.. code-block:: bash

    $ app/console -e=test behat @AcmeDemoBundle --definitions --lang=ru

All your mink steps will be executed against default Mink session (``symfony``
by default).

.. tip::

    Default session could be easily changed with ``default_session`` option
    in MinkBundle config:

    .. code-block:: yaml

        # app/config/config_test.yml

        mink:
          default_session:  goutte
          goutte:           ~

If you need to run javascript or UI related steps, you'll need to tag your
UI/JS scenario with ``@javascript`` tag:

.. code-block:: gherkin

    ...

    @javascript
    Scenario: Drag'n'Drop scenario
      ...

Default session for such scenario will become ``sahi``, giving you access to all
js-specific functionality of the Mink.

.. tip::

    ``sahi`` session will automatically start firefox browser for every ``@javascript``
    scenario. If you want to run your sahi scenario in different browser -
    you can configure it under the ``browser_name`` option in the MinkBunde config:

    .. code-block:: yaml

        # app/config/config_test.yml

        mink:
          browser_name:  chrome
          sahi:          ~

.. tip::

    Default javascript session could be easily changed to Zombie.js with
    ``javascript_session`` option in MinkBundle config:

    .. code-block:: yaml

        # app/config/config_test.yml

        mink:
          javascript_session:  zombie
          zombie:              ~

Also, you can switch all scenarios inside single feature to ``@javascript``
session by tagging feature instead:

.. code-block:: gherkin

    @javascript
    Feature: My web feature
      In order to ...
      As a ...
      I need to ...

      Scenario: Scenario 1
        ...

      Scenario: Scenario 2
        ...

If you want to switch to specific Mink session instead, you can do it with
``@mink:...`` tag:

.. code-block:: gherkin

    ...

    @mink:zombie
    Scenario: Drag'n'Drop in Zombie.js
      ...

Running Features
----------------

The simplest possible way to run all features in your project is to call
``behat`` command without arguments:

.. code-block:: bash

    $ app/console -e=test behat

This way, BehatBundle will run features suite from every **registered** bundle,
that has one.

More proper way is to run specific bundle feature suite. You can do it with
either full path to bundle:

.. code-block:: bash

    $ app/console -e=test behat src/Acme/DemoBundle

or with full bundle name, prefixed with ``@``:

.. code-block:: bash

    $ app/console -e=test behat @AcmeDemoBundle

And of course, BehatBundle supports specific feature call:

.. code-block:: bash

    $ app/console -e=test behat src/Acme/DemoBundle/Features/my.feature

Or even specific scenario call:

.. code-block:: bash

    $ app/console -e=test behat src/Acme/DemoBundle/Features/my.feature:5

.. tip::

    You can use short notation to call features and scenarios too:

    .. code-block:: bash

        $ app/console -e=test behat @AcmeDemoBundle/my.feature:5

Also, BehatBundle supports almost all `configuration options
<http://docs.behat.org/guides/7.config.html>`_, that Behat does.

Read More About Behat & Mink
----------------------------

If you don't know what Behat is and how it could help your development become
more successfull, read: `Behat Quick Intro <http://docs.behat.org/quick_intro.html>`_.

If you wan to describe your web application with Mink steps, you should read
about Mink on its `official site <http://mink.behat.org>`_.
