<?php

namespace JMS\SerializerBundle;

use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;

interface SerializerBundleAwareInterface
{
    function configureSerializerExtension(JMSSerializerExtension $extension);
}
