<?php

namespace JMS\SerializerBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Accessor
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

        if (isset($values['value'])) {
            $values['getter'] = $values['value'];
        }
        if (isset($values['getter'])) {
            if (!is_string($values['getter'])) {
                throw new \InvalidArgumentException(sprintf('"getter" attribute of annotation @Accessor must be a string, but got %s.', json_encode($values['getter'])));
            }
            $this->getter = $values['getter'];
        }

        if (isset($values['setter'])) {
            if (!is_string($values['setter'])) {
                throw new \InvalidArgumentException(sprintf('"setter" attribute of annotation @Accessor must be a string, but got %s.', json_encode($values['setter'])));
            }
            $this->setter = $values['setter'];
        }
    }
}