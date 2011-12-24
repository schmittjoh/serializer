<?php

namespace JMS\SerializerBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class AccessType
{
    /**
     * @Required
     * @var string
     */
    public $type;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

        if (isset($values['value'])) {
            $values['type'] = $values['value'];
        }
        if (!isset($values['type'])) {
            throw new \InvalidArgumentException(sprintf('@AccessType requires the AccessType.'));
        }
        if (!is_string($values['type'])) {
            throw new \InvalidArgumentException(sprintf('@AccessType expects a string type, but got %s.', json_encode($values['type'])));
        }
        $this->type = $values['type'];
    }
}