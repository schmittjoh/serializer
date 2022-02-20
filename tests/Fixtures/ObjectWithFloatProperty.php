<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class ObjectWithFloatProperty
{
    /**
     * @Type("float")
     * @var float
     */
    #[Type(name: 'float')]
    private $floatingPointUnchanged;

    /**
     * @Type("float<2,'HALF_DOWN'>")
     * @var float
     */
    #[Type(name: 'float<2, "HALF_DOWN">')]
    private $floatingPointHalfDown;

    /**
     * @Type("double<1, 'HALF_EVEN'>")
     * @var float
     */
    #[Type(name: 'double<1, "HALF_EVEN">')]
    private $floatingPointHalfEven;

    /**
     * @Type("float<1, 'HALF_ODD'>")
     * @var float
     */
    #[Type(name: 'float<1, "HALF_ODD">')]
    private $floatingPointHalfOdd;

    /**
     * @Type("double<2>")
     * @var float
     */
    #[Type(name: 'double<2, "HALF_UP">')]
    private $floatingPointHalfUp;

    public function __construct(
        float $floatingPointUnchanged,
        float $floatingPointHalfDown,
        float $floatingPointHalfEven,
        float $floatingPointHalfOdd,
        float $floatingPointHalfUp
    ) {
        $this->floatingPointUnchanged = $floatingPointUnchanged;
        $this->floatingPointHalfDown = $floatingPointHalfDown;
        $this->floatingPointHalfEven = $floatingPointHalfEven;
        $this->floatingPointHalfOdd = $floatingPointHalfOdd;
        $this->floatingPointHalfUp = $floatingPointHalfUp;
    }

    public function getFloatingPointUnchanged(): float
    {
        return $this->floatingPointUnchanged;
    }

    public function getFloatingPointHalfDown(): float
    {
        return $this->floatingPointHalfDown;
    }

    public function getFloatingPointHalfEven(): float
    {
        return $this->floatingPointHalfEven;
    }

    public function getFloatingPointHalfOdd(): float
    {
        return $this->floatingPointHalfOdd;
    }

    public function getFloatingPointHalfUp(): float
    {
        return $this->floatingPointHalfUp;
    }
}
