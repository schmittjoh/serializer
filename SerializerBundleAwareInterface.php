<?php

namespace JMS\SerializerBundle;

interface SerializerBundleAwareInterface
{
    function configureSerializerExtension(JMSSerializerExtension $extension);
}