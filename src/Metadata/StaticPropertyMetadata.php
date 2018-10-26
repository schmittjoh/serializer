<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata;

class StaticPropertyMetadata extends PropertyMetadata
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * StaticPropertyMetadata constructor.
     *
     * @param mixed $fieldValue
     * @param array $groups
     */
    public function __construct(string $className, string $fieldName, $fieldValue, array $groups = [])
    {
        $this->class = $className;
        $this->name = $fieldName;
        $this->serializedName = $fieldName;
        $this->value = $fieldValue;
        $this->readOnly = true;
        $this->groups = $groups;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setAccessor(string $type, ?string $getter = null, ?string $setter = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->value,
            parent::serialize(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        $parentStr = $this->unserializeProperties($str);
        [$this->class, $this->name] = unserialize($parentStr);
    }

    protected function unserializeProperties(string $str): string
    {
        [
            $this->value,
            $parentStr,
        ] = unserialize($str);
        return parent::unserializeProperties($parentStr);
    }
}
