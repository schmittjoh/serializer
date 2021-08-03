<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

use JMS\Serializer\Exception\InvalidArgumentException;

trait AnnotationUtilsTrait
{
    private function loadAnnotationParameters(array $vars): void
    {
        if (!array_key_exists('values', $vars)) {
            $values = [];
        } elseif (!is_array($vars['values'])) {
            $values = ['value' => $vars['values']];
        } else {
            $values = $vars['values'];
        }

        unset($vars['values']);

        if (array_key_exists('value', $values)) {
            $values[key($vars)] = $values['value'];
            unset($values['value']);
        }

        foreach ($values as $key => $value) {
            $vars[$key] = $value;
        }

        foreach ($vars as $key => $value) {
            if (!property_exists(static::class, $key)) {
                throw new InvalidArgumentException(sprintf('Unknown property "%s" on annotation "%s".', $key, static::class));
            }

            $this->{$key} = $value;
        }
    }
}
