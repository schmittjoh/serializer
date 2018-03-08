<?php

namespace JMS\Serializer\Accessor\Guess;

/**
 * Find for accessors in "getFooBar" and "setFooBar" format.
 */
final class DefaultAccessorFinder implements AccessorFinderInterface
{
    /**
     * {@inheritDoc}
     */
    public function findGetter(\ReflectionClass $class, $propertyName)
    {
        return $this->findAccessor('get', $propertyName, $class);
    }

    /**
     * {@inheritDoc}
     */
    public function findSetter(\ReflectionClass $class, $propertyName)
    {
        return $this->findAccessor('set', $propertyName, $class);
    }

    /**
     * @param $prefix
     * @param $property
     * @param \ReflectionClass $class
     * @return null|string
     */
    private function findAccessor($prefix, $property, \ReflectionClass $class)
    {
        $method = $prefix . $this->toStudyCase($property);

        return $class->hasMethod($method) ? $method : null;
    }

    /**
     * @param string $value
     * @return string
     */
    private function toStudyCase($value)
    {
        // "foo-bar_baz" -> "fooBarBaz":
        $value = \preg_replace_callback('/[\-\_](?<letter>\w)/', function (array $matches) {
            return strtoupper($matches['letter']);
        }, $value);

        return ucfirst($value);
    }
}
