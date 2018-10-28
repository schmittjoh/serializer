XML Reference
-------------
::

    <!-- MyBundle\Resources\config\serializer\Fully.Qualified.ClassName.xml -->
    <?xml version="1.0" encoding="UTF-8" ?>
    <serializer>
        <class name="Fully\Qualified\ClassName" exclusion-policy="ALL" xml-root-name="foo-bar" exclude="true"
            accessor-order="custom" custom-accessor-order="propertyName1,propertyName2,...,propertyNameN"
            access-type="public_method" discriminator-field-name="type" discriminator-disabled="false" read-only="false">
            <xml-namespace prefix="atom" uri="http://www.w3.org/2005/Atom"/>
            <xml-discriminator attribute="true" cdata="false" namespace=""/>
            <discriminator-class value="some-value">ClassName</discriminator-class>
            <discriminator-groups>
                <group>foo</group>
            </discriminator-groups>
            <property name="some-property"
                      exclude="true"
                      expose="true"
                      exclude-if="expr"
                      expose-if="expr"
                      deserialize-null="false"
                      skip-when-empty="false"
                      type="string"
                      serialized-name="foo"
                      since-version="1.0"
                      until-version="1.1"
                      xml-attribute="true"
                      xml-value="true"
                      access-type="public_method"
                      accessor-getter="getSomeProperty"
                      accessor-setter="setSomeProperty"
                      inline="true"
                      read-only="true"
                      groups="foo,bar"
                      xml-key-value-pairs="true"
                      xml-attribute-map="true"
                      max-depth="2"
            >
                <!-- You can also specify the type as element which is necessary if
                     your type contains "<" or ">" characters. -->
                <type><![CDATA[]]></type>
                <xml-list inline="true" entry-name="foobar" namespace="http://www.w3.org/2005/Atom" skip-when-empty="true" />
                <xml-map inline="true" key-attribute-name="foo" entry-name="bar" namespace="http://www.w3.org/2005/Atom" />
                <xml-element cdata="false" namespace="http://www.w3.org/2005/Atom"/>
                <groups>
                    <value>foo</value>
                    <value>bar</value>
                </groups>
            </property>
            <callback-method name="foo" type="pre-serialize" />
            <callback-method name="bar" type="post-serialize" />
            <callback-method name="baz" type="post-deserialize" />

            <virtual-property method="public_method"
                      name="some-property"
                      exclude="true"
                      expose="true"
                      skip-when-empty="false"
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
                      max-depth="2"
            >
            <virtual-property expression="object.getName()"
                      name="some-property"
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
                      max-depth="2"
            >
                <!-- You can also specify the type as element which is necessary if
                     your type contains "<" or ">" characters. -->
                <type><![CDATA[]]></type>
                <groups>
                    <value>foo</value>
                    <value>bar</value>
                </groups>
                <xml-list inline="true" entry-name="foobar" namespace="http://www.w3.org/2005/Atom" skip-when-empty="true" />
                <xml-map inline="true" key-attribute-name="foo" entry-name="bar" namespace="http://www.w3.org/2005/Atom" />
            </virtual-property>
            
        </class>
    </serializer>
