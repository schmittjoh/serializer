XML Reference
-------------
::

    <!-- MyBundle\Resources\config\serializer\ClassName.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <serializer>
        <class name="Fully\Qualified\ClassName" exclusion-policy="ALL" xml-root-name="foo-bar" exclude="true"
            accessor-order="custom" custom-accessor-order="propertyName1,propertyName2,...,propertyNameN"
            access-type="public_method" discriminator-field-name="type">
            <discriminator-class value="some-value">ClassName</discriminator-class>
            <property name="some-property"
                      exclude="true"
                      expose="true"
                      type="string"
                      serialized-name="foo"
                      since-version="1.0"
                      until-version="1.1"
                      xml-attribute="true"
                      access-type="public_method"
                      accessor-getter="getSomeProperty"
                      accessor-setter="setSomeProperty"
                      inline="true"
                      read-only="true"
                      groups="foo,bar"
                      xml-key-value-pairs="true"
                      xml-attribute-map="true"
            >
                <!-- You can also specify the type as element which is necessary if
                     your type contains "<" or ">" characters. -->
                <type><![CDATA[]]></type>
                <xml-list inline="true" entry-name="foobar" />
                <xml-map inline="true" key-attribute-name="foo" entry-name="bar" />
            </property>
            <callback-method name="foo" type="pre-serialize" />
            <callback-method name="bar" type="post-serialize" />
            <callback-method name="baz" type="post-deserialize" />
            <callback-method name="serializeToXml" type="handler" direction="serialization" format="xml" />
            <callback-method name="deserializeFromJson" type="handler" direction="deserialization" format="xml" />
        </class>
    </serializer>
