Installation
============

1. Using Composer (recommended)
-------------------------------

To install JMSSerializerBundle with Composer just add the following to your
`composer.json` file:

.. code-block :: js

    // composer.json
    {
        // ...
        require: {
            // ...
            "jms/serializer-bundle": "dev-master"
        }
    }
    
.. note ::

    Please replace `dev-master` in the snippet above with the latest stable
    branch, for example ``1.0.*``.
    
Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

.. code-block :: bash

    $ php composer.phar update
    
Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        // ...
    );
    
2. Using the ``deps`` file (Symfony 2.0.x)
------------------------------------------

First, checkout a copy of the code. Just add the following to the ``deps`` 
file of your Symfony Standard Distribution:

.. code-block :: ini

    [JMSSerializerBundle]
        git=git://github.com/schmittjoh/JMSSerializerBundle.git
        target=bundles/JMS/SerializerBundle

Then register the bundle with your kernel:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        // ...
    );

This bundle also requires the Metadata library (**you need the 1.1 version, not the 1.0
version** which ships with the Symfony Standard Edition.):

.. code-block :: ini

    [metadata]
        git=http://github.com/schmittjoh/metadata.git
        version=1.1.0

Make sure that you also register the namespaces with the autoloader:

.. code-block :: php

    <?php

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'JMS'              => __DIR__.'/../vendor/bundles',
        'Metadata'         => __DIR__.'/../vendor/metadata/src',
        // ...
    ));

Now use the ``vendors`` script to clone the newly added repositories 
into your project:

.. code-block :: bash

    $ php bin/vendors install
