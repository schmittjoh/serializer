Serializer
==========

.. image:: logo-small.png

Introduction
------------
This library allows you to (de-)serialize data of any complexity. Currently, it supports XML and JSON.

It also provides you with a rich tool-set to adapt the output to your specific needs.

Built-in features include:

- (De-)serialize data of any complexity; circular references are handled gracefully.
- Supports many built-in PHP types (such as dates)
- Integrates with Doctrine ORM, et. al.
- Supports versioning, e.g. for APIs
- Configurable via XML, YAML, or Doctrine Annotations

Installation
------------
This library can be easily installed via composer

.. code-block :: bash

    composer require jms/serializer

or just add it to your ``composer.json`` file directly.

Usage
-----
For standalone projects usage of the provided builder is encouraged::

    $serializer = JMS\Serializer\SerializerBuilder::create()->build();
    $jsonContent = $serializer->serialize($data, 'json');
    echo $jsonContent; // or return it in a Response


Documentation
-------------

.. toctree ::
    :hidden:

    configuration
    usage
    event_system
    handlers
    reference
    cookbook

- :doc:`Configuration <configuration>`
- :doc:`Usage <usage>`
- :doc:`Events <event_system>`
- :doc:`Handlers <handlers>`

- Recipes
    * :doc:`/cookbook/exclusion_strategies`

- Reference
    * :doc:`Annotations </reference/annotations>`
    * :doc:`XML Reference </reference/xml_reference>`
    * :doc:`YML Reference </reference/yml_reference>`

License
-------

The code is released under the business-friendly `MIT license`_.

Documentation is subject to the `Attribution-NonCommercial-NoDerivs 3.0 Unported
license`_.

.. _MIT license: https://opensource.org/licenses/MIT
.. _Attribution-NonCommercial-NoDerivs 3.0 Unported license: http://creativecommons.org/licenses/by-nc-nd/3.0/

