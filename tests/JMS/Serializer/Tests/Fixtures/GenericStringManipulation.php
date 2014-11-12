<?php


namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\GenericAccessor;

trait GenericStringManipulation
{

    /**
     * @var string
     *
     * @Type("string")
     * @GenericAccessor(getter="getStringAsUpperCase", setter="setStringAsLowerCase", propertyName="testString")
     */
    protected $testString;

    public function getTestString()
    {
        return $this->testString;
    }

    public function setTestString($string)
    {
        $this->testString = $string;
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
