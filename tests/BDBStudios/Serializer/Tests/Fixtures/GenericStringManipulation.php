<?php


namespace BDBStudios\Serializer\Tests\Fixtures;

use BDBStudios\Serializer\Annotation\Type;
use BDBStudios\Serializer\Annotation\GenericAccessor;

class GenericStringManipulation
{

    /**
     * @var string
     *
     * @Type("string")
     * @GenericAccessor(getter="getStringAsUpperCase", setter="setStringAsLowerCase", propertyName="propertyOne")
     */
    protected $propertyOne;

    /**
     * @var string
     *
     * @Type("string")
     * @GenericAccessor(getter="getStringAsUpperCase", setter="setStringAsLowerCase", propertyName="propertyTwo")
     */
    protected $propertyTwo;

    /**
     * @param $value
     */
    public function setPropertyOne($value)
    {
        $this->propertyOne = $value;
    }

    /**
     * @param $value
     */
    public function setPropertyTwo($value)
    {
        $this->propertyTwo = $value;
    }

    /**
     * @return string
     */
    public function getPropertyOne()
    {
        return $this->propertyOne;
    }

    /**
     * @return string
     */
    public function getPropertyTwo()
    {
        return $this->propertyTwo;
    }

    /**
     * @param $propertyName
     * @return string
     */
    public function getStringAsUpperCase($propertyName)
    {
        if (property_exists(get_class($this), $propertyName)) {
            return strtoupper($this->{$propertyName});
        }

        return  '';
    }

    /**
     * @param $value
     * @param $propertyName
     * @return $this
     */
    public function setStringAsLowerCase($value, $propertyName)
    {
        if (property_exists(get_class($this), $propertyName)) {
            $this->{$propertyName} = strtolower($value);
        }

        return $this;
    }
}
