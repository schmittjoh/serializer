YAML Reference
--------------

.. code-block :: yaml

    # Vendor\MyBundle\Resources\config\serializer\Model.ClassName.yml
    Vendor\MyBundle\Model\ClassName:
        exclusion_policy: ALL
        xml_root_name: foobar
        xml_root_namespace: http://your.default.namespace
        exclude: true
        exclude_if: expr
        read_only: false
        access_type: public_method # defaults to property
        accessor_order: custom
        custom_accessor_order: [propertyName1, propertyName2, ..., propertyNameN]
        discriminator:
            field_name: type
            disabled: false
            map:
                some-value: ClassName
            groups: [foo, bar]
            xml_attribute: true
            xml_element:
                cdata: false
                namespace: http://www.w3.org/2005/Atom
        virtual_properties:
            getSomeProperty:
                name: optional-prop-name
                serialized_name: foo
                type: integer
            expression_prop:
                name: optional-prop-name
                exp: object.getName()
                serialized_name: foo
                type: integer
        xml_namespaces:
            "": http://your.default.namespace
            atom: http://www.w3.org/2005/Atom
        properties:
            some-property:
                exclude: true
                expose: true
                exclude_if: expr
                expose_if: expr
                skip_when_empty: false
                access_type: public_method # defaults to property
                accessor: # access_type must be set to public_method
                    getter: getSomeOtherProperty
                    setter: setSomeOtherProperty
                type: string
                serialized_name: foo
                since_version: 1.0
                until_version: 1.1
                groups: [foo, bar]
                xml_attribute: true
                xml_value: true
                inline: true
                read_only: true
                xml_key_value_pairs: true
                xml_list:
                    inline: true
                    entry_name: foo
                    namespace: http://www.w3.org/2005/Atom
                xml_map:
                    inline: true
                    key_attribute_name: foo
                    entry_name: bar
                    namespace: http://www.w3.org/2005/Atom
                xml_attribute_map: true
                xml_element:
                    cdata: false
                    namespace: http://www.w3.org/2005/Atom
                max_depth: 2

        callback_methods:
            pre_serialize: [foo, bar]
            post_serialize: [foo, bar]
            post_deserialize: [foo, bar]

Constants
---------

In some cases, it may be helpful to reference constants in your YAML files.
You can do this by prefixing the constant with the special `!php/const` syntax.

.. code-block :: yaml

    Vendor\MyBundle\Model\ClassName:
        properties:
            some-property:
                serialized_name: !php/const Vendor\MyBundle\Model\ClassName::SOME_CONSTANT
