<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\InvalidArgumentException;
use function property_exists;
use function sprintf;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
final class VirtualProperty
{
    public $exp;
    public $name;
    public $options = [];

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            if (!property_exists(__CLASS__, $key)) {
                throw new InvalidArgumentException(sprintf('Unknown property "%s" on annotation "%s".', $key, __CLASS__));
            }
            $this->{$key} = $value;
        }
    }
}
