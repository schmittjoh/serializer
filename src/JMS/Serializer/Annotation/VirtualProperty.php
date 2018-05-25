<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 *
 * @author Alexander Klimenkov <alx.devel@gmail.com>
 */
final class VirtualProperty
{
    public $exp;
    public $name;
    public $options = array();

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            if (!property_exists(__CLASS__, $key)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, __CLASS__));
            }
            $this->{$key} = $value;
        }
    }
}

