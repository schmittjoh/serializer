YAML Reference
--------------
::

    # MyBundle\Resources\config\serializer\Fully.Qualified.ClassName.yml
    Fully\Qualified\ClassName:
        exclusion_policy: ALL
        xml_root_name: foobar
        exclude: true
        read_only: false
        access_type: public_method # defaults to property
        accessor_order: custom
        custom_accessor_order: [propertyName1, propertyName2, ..., propertyNameN]
        discriminator:
            field_name: type
            map:
                some-value: ClassName
        virtual_properties:
            getSomeProperty:
                serialized_name: foo
                type: integer
        xml_namespaces:
            "": http://your.default.namespace
            atom: http://www.w3.org/2005/Atom
        properties:
            some-property:
                exclude: true
                expose: true
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
                xml_map:
                    inline: true
                    key_attribute_name: foo
                    entry_name: bar
                xml_attribute_map: true
                xml_element:
                    cdata: false
                    namespace: http://www.w3.org/2005/Atom
                max_depth: 2

        handler_callbacks:
            serialization:
                xml: serializeToXml
                json: serializeToJson
            deserialization:
                xml: deserializeFromXml

        callback_methods:
            pre_serialize: [foo, bar]
            post_serialize: [foo, bar]
            post_deserialize: [foo, bar]
