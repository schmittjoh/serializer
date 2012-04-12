JMSSerializerBundle
===================

Introduction
------------

JMSSerializerBundle allows you to serialize your objects into a requested
output format such as JSON, XML, or YAML, and vice versa. It provides you 
with a rich toolset which empowers you to adapt the output to your specific
needs.

Built-in features include:

- (de-)serialize object graphs of any complexity including circular references
- supports many built-in PHP types (such as dates)
- integrates with Doctrine ORM, et. al.
- supports versioning, e.g. for APIs
- configurable via PHP, XML, YAML, or annotations

Documentation
-------------

.. toctree ::
    :hidden:
    
    installation
    configuration
    usage
    reference
    cookbook

- :doc:`Installation <installation>`
- :doc:`Configuration <configuration>`
- :doc:`Usage <usage>`
- Recipies
    * :doc:`Custom Handlers </cookbook/custom_handlers>`
    * :doc:`/cookbook/exclusion_strategies`
    * :doc:`/cookbook/metadata_for_third_party`
- Reference
    * :doc:`Annotations </reference/annotations>`
    * :doc:`XML Reference </reference/xml_reference>`
    * :doc:`YML Reference </reference/yml_reference>`

License
-------

The code is released under the business-friendly `Apache2 license`_. 

Documentation is subject to the `Attribution-NonCommercial-NoDerivs 3.0 Unported
license`_.

.. _Apache2 license: http://www.apache.org/licenses/LICENSE-2.0.html
.. _Attribution-NonCommercial-NoDerivs 3.0 Unported license: http://creativecommons.org/licenses/by-nc-nd/3.0/

