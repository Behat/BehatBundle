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

Now you should include Behat's autoloader into your ``app/autoload.php``:

.. code-block:: php

    <?php
    
    //...
    
    // remove Symfony2 classes from Behat autoload
    // routine, cuz Symfony2 already autoloads them
    // by itself
    define('BEHAT_AUTOLOAD_ZF2', false);

    // require autoloader
    require_once 'behat/autoload.php';

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

Every bundle in your application could have it's own feature suite. And every
feature suite has it's own independent context class (``FeatureContext``).

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

Mink Integration: ``MinkContext``
---------------------------------

By default, BehatBundle creates ``FeatureContext``, that inherit from simplest
``Behat\BehatBundle\Context\BehatContext`` class. But if you want to test your
app with `Mink <http://mink.behat.org>`_ web acceptance testing framework - you
should extend ``Behat\BehatBundle\Context\MinkContext`` instead.

.. note::

    In order to be able to use ``MinkContext``, you should install and configure
    `MinkBundle <http://mink.behat.org/bundle>`_ first.

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

Read More About Behat
---------------------

If you don't know what Behat is and how it could help your development become
more successfull, read: `Behat Quick Intro <http://docs.behat.org/quick_intro.html>`_.

