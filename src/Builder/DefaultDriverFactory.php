<?php

declare(strict_types=1);

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\Driver\AnnotationOrAttributeDriver;
use JMS\Serializer\Metadata\Driver\DefaultValuePropertyDriver;
use JMS\Serializer\Metadata\Driver\EnumPropertiesDriver;
use JMS\Serializer\Metadata\Driver\NullDriver;
use JMS\Serializer\Metadata\Driver\TypedPropertiesDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use Symfony\Component\Yaml\Yaml;

final class DefaultDriverFactory implements DriverFactoryInterface
{
    /**
     * @var ParserInterface
     */
    private $typeParser;

    /**
     * @var bool
     */
    private $enableEnumSupport = false;

    /**
     * @var PropertyNamingStrategyInterface
     */
    private $propertyNamingStrategy;

    /**
     * @var CompilableExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(PropertyNamingStrategyInterface $propertyNamingStrategy, ?ParserInterface $typeParser = null, ?CompilableExpressionEvaluatorInterface $expressionEvaluator = null)
    {
        $this->typeParser = $typeParser ?: new Parser();
        $this->propertyNamingStrategy = $propertyNamingStrategy;
        $this->expressionEvaluator = $expressionEvaluator;
    }

    public function enableEnumSupport(bool $enableEnumSupport = true): void
    {
        $this->enableEnumSupport = $enableEnumSupport;
    }

    public function createDriver(array $metadataDirs, ?Reader $annotationReader = null): DriverInterface
    {
        if (PHP_VERSION_ID < 80000 && empty($metadataDirs) && !interface_exists(Reader::class)) {
            throw new RuntimeException(sprintf('To use "%s", either a list of metadata directories must be provided, the "doctrine/annotations" package installed, or use PHP 8.0 or later.', self::class));
        }

        /*
         * Build the sorted list of metadata drivers based on the environment. The final order should be:
         *
         * - YAML Driver
         * - XML Driver
         * - Annotations/Attributes Driver
         * - Null (Fallback) Driver
         */
        $metadataDrivers = [];

        if (PHP_VERSION_ID >= 80000 || $annotationReader instanceof Reader) {
            $metadataDrivers[] = new AnnotationOrAttributeDriver($this->propertyNamingStrategy, $this->typeParser, $this->expressionEvaluator, $annotationReader);
        }

        if (!empty($metadataDirs)) {
            $fileLocator = new FileLocator($metadataDirs);

            array_unshift($metadataDrivers, new XmlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser, $this->expressionEvaluator));

            if (class_exists(Yaml::class)) {
                array_unshift($metadataDrivers, new YamlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser, $this->expressionEvaluator));
            }
        }

        $driver = new DriverChain($metadataDrivers);
        $driver->addDriver(new NullDriver($this->propertyNamingStrategy));

        if ($this->enableEnumSupport) {
            $driver = new EnumPropertiesDriver($driver);
        }

        $driver = new TypedPropertiesDriver($driver, $this->typeParser);

        if (PHP_VERSION_ID >= 80000) {
            $driver = new DefaultValuePropertyDriver($driver);
        }

        return $driver;
    }
}
