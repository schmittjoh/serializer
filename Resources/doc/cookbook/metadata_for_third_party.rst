Overriding Metadata Provided by Third-Parties
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Sometimes you want to serialize objects which are shipped by a third-party bundle. 
Such a third-party bundle might not ship with metadata that suits your needs, or 
possibly none, at all. In such a case, you can override the default location that
is searched for metadata with a path that is under your control.

.. configuration-block ::

    .. code-block :: yaml
    
        jms_serializer:
            metadata:
                directories:
                    FOSUB:
                        namespace_prefix: "FOS\\UserBundle"
                        path: "%kernel.root_dir%/serializer/FOSUB"

    .. code-block :: xml
    
        <jms-serializer>
            <metadata>
                <directory namespace_prefix="FOS\UserBundle"
                           path="%kernel.root_dir%/serializer/FOSUB" />
            </metadata>
        </jms-serializer>