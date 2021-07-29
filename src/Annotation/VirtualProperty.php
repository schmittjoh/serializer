<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\InvalidArgumentException;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 *
 * @author Alexander Klimenkov <alx.devel@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class VirtualProperty
{
    /**
     * @var string|null
     */
    public $exp = null;

    /**
     * @var string|null
     */
    public $name = null;

    /**
     * @var array
     */
    public $options = [];

    public function __construct(array $data = [], ?string $name = null, ?string $exp = null, ?array $options = [])
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            if (!property_exists(self::class, $key)) {
                throw new InvalidArgumentException(sprintf('Unknown property "%s" on annotation "%s".', $key, self::class));
            }

            $this->{$key} = $value;
        }

        if (null !== $name) {
            $this->name = $name;
        }

        if (null !== $exp) {
            $this->exp = $exp;
        }

        if (0 !== count($options)) {
            $this->options = $options;
        }

        foreach ($options as $option) {
            if (is_array($option) && class_exists($option[0])) {
                $this->options[] = new $option[0]([], ...$option[1]);

                continue;
            }

            $this->options[] = $option;
        }
    }
}
