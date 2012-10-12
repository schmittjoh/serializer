Configuration
=============

Initial Configuration
---------------------
JMSSerializerBundle requires no initial configuration to get you started.

Reference
---------

Below you find a reference of all configuration options with their default
values:

.. configuration-block ::

    .. code-block :: yaml

        # config.yml
        jms_serializer:
            handlers:
                datetime:
                    default_format: "c" # ISO8601
                    default_timezone: "UTC" # defaults to whatever timezone set in php.ini or via date_default_timezone_set

            property_naming:
                separator:  _
                lower_case: true

            metadata:
                cache: file
                debug: "%kernel.debug%"
                file_cache:
                    dir: "%kernel.cache_dir%/serializer"

                # Using auto-detection, the mapping files for each bundle will be
                # expected in the Resources/config/serializer directory.
                #
                # Example:
                # class: My\FooBundle\Entity\User
                # expected path: @MyFooBundle/Resources/config/serializer/Entity.User.(yml|xml|php)
                auto_detection: true

                # if you don't want to use auto-detection, you can also define the
                # namespace prefix and the corresponding directory explicitly
                directories:
                    any-name:
                        namespace_prefix: "My\\FooBundle"
                        path: "@MyFooBundle/Resources/config/serializer"
                    another-name:
                        namespace_prefix: "My\\BarBundle"
                        path: "@MyBarBundle/Resources/config/serializer"

            visitors:
                json:
                    options: 0 # json_encode options bitmask
                    serialize_null: false # whether to preserve keys with null values
                xml:
                    serialize_null: false
                    doctype_whitelist:
                        - '<!DOCTYPE authorized SYSTEM "http://some_url">' # an authorized document type for xml deserialization
                yaml:
                    serialize_null: false

    .. code-block :: xml

        <!-- config.xml -->
        <jms-serializer>
            <handlers>
                <object-based />
                <datetime
                    format="Y-mdTH:i:s"
                    default-timezone="UTC" />
                <array-collection />
                <form-error />
                <constraint-violation />
            </handlers>

            <property-naming
                seperator="_"
                lower-case="true" />

            <metadata
                cache="file"
                debug="%kernel.debug%"
                auto-detection="true">

                <file-cache dir="%kernel.cache_dir%/serializer" />

                <!-- If auto-detection is enabled, mapping files for each bundle will
                     be expected in the Resources/config/serializer directory.

                     Example:
                     class: My\FooBundle\Entity\User
                     expected path: @MyFooBundle/Resources/config/serializer/Entity.User.(yml|xml|php)
                -->
                <directory
                    namespace-prefix="My\FooBundle"
                    path="@MyFooBundle/Resources/config/serializer" />
            </metadata>

            <visitors>
                <xml>
                    <whitelisted-doctype><![CDATA[<!DOCTYPE...>]]></whitelisted-doctype>
                    <whitelisted-doctype><![CDATA[<!DOCTYPE...>]]></whitelisted-doctype>
                </xml>
            </visitors>
        </jms-serializer>
