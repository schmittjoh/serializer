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
                object_based: false
                datetime:
                    format: "Y-m-dTH:i:s" # ISO8601
                    default_timezone: "UTC" # defaults to whatever timezone set in php.ini or via date_default_timezone_set
                array_collection: true
                form_error: true
                constraint_violation: true
    
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
        </jms-serializer>
    
