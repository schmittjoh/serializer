<?php

declare(strict_types=1);

namespace JMS\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\FilesystemCache;
use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Builder\DocBlockDriverFactory;
use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\ContextFactory\CallableDeserializationContextFactory;
use JMS\Serializer\ContextFactory\CallableSerializationContextFactory;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\GraphNavigator\Factory\DeserializationGraphNavigatorFactory;
use JMS\Serializer\GraphNavigator\Factory\GraphNavigatorFactoryInterface;
use JMS\Serializer\GraphNavigator\Factory\SerializationGraphNavigatorFactory;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Handler\IteratorHandler;
use JMS\Serializer\Handler\StdClassHandler;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\SerializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\XmlDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\XmlSerializationVisitorFactory;
use Metadata\Cache\CacheInterface;
use Metadata\Cache\FileCache;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Builder for serializer instances.
 *
 * This object makes serializer construction a breeze for projects that do not use
 * any special dependency injection container.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class SerializerBuilder
{
    /**
     * @var string[]
     */
    private $metadataDirs = [];

    /**
     * @var HandlerRegistryInterface
     */
    private $handlerRegistry;

    /**
     * @var bool
     */
    private $handlersConfigured = false;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $listenersConfigured = false;

    /**
     * @var ObjectConstructorInterface
     */
    private $objectConstructor;

    /**
     * @var SerializationVisitorFactory[]
     */
    private $serializationVisitors;

    /**
     * @var DeserializationVisitorFactory[]
     */
    private $deserializationVisitors;

    /**
     * @var bool
     */
    private $visitorsAdded = false;

    /**
     * @var PropertyNamingStrategyInterface
     */
    private $propertyNamingStrategy;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $includeInterfaceMetadata = false;

    /**
     * @var DriverFactoryInterface
     */
    private $driverFactory;

    /**
     * @var SerializationContextFactoryInterface
     */
    private $serializationContextFactory;

    /**
     * @var DeserializationContextFactoryInterface
     */
    private $deserializationContextFactory;

    /**
     * @var ParserInterface
     */
    private $typeParser;

    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    /**
     * @var AccessorStrategyInterface
     */
    private $accessorStrategy;

    /**
     * @var CacheInterface
     */
    private $metadataCache;

    /**
     * @var bool
     */
    private $docBlockTyperResolver;

    /**
     * @param mixed ...$args
     *
     * @return SerializerBuilder
     */
    public static function create(...$args): self
    {
        return new static(...$args);
    }

    public function __construct(?HandlerRegistryInterface $handlerRegistry = null, ?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->typeParser = new Parser();
        $this->handlerRegistry = $handlerRegistry ?: new HandlerRegistry();
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
        $this->serializationVisitors = [];
        $this->deserializationVisitors = [];

        if ($handlerRegistry) {
            $this->handlersConfigured = true;
        }

        if ($eventDispatcher) {
            $this->listenersConfigured = true;
        }
    }

    public function setAccessorStrategy(AccessorStrategyInterface $accessorStrategy): self
    {
        $this->accessorStrategy = $accessorStrategy;

        return $this;
    }

    private function getAccessorStrategy(): AccessorStrategyInterface
    {
        if (!$this->accessorStrategy) {
            $this->accessorStrategy = new DefaultAccessorStrategy($this->expressionEvaluator);
        }

        return $this->accessorStrategy;
    }

    public function setExpressionEvaluator(ExpressionEvaluatorInterface $expressionEvaluator): self
    {
        $this->expressionEvaluator = $expressionEvaluator;

        return $this;
    }

    public function setTypeParser(ParserInterface $parser): self
    {
        $this->typeParser = $parser;

        return $this;
    }

    public function setAnnotationReader(Reader $reader): self
    {
        $this->annotationReader = $reader;

        return $this;
    }

    public function setDebug(bool $bool): self
    {
        $this->debug = $bool;

        return $this;
    }

    public function setCacheDir(string $dir): self
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new InvalidArgumentException(sprintf('The cache directory "%s" is not writable.', $dir));
        }

        $this->cacheDir = $dir;

        return $this;
    }

    public function addDefaultHandlers(): self
    {
        $this->handlersConfigured = true;
        $this->handlerRegistry->registerSubscribingHandler(new DateHandler());
        $this->handlerRegistry->registerSubscribingHandler(new StdClassHandler());
        $this->handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
        $this->handlerRegistry->registerSubscribingHandler(new IteratorHandler());

        return $this;
    }

    public function configureHandlers(\Closure $closure): self
    {
        $this->handlersConfigured = true;
        $closure($this->handlerRegistry);

        return $this;
    }

    public function addDefaultListeners(): self
    {
        $this->listenersConfigured = true;
        $this->eventDispatcher->addSubscriber(new DoctrineProxySubscriber());

        return $this;
    }

    public function configureListeners(\Closure $closure): self
    {
        $this->listenersConfigured = true;
        $closure($this->eventDispatcher);

        return $this;
    }

    public function setObjectConstructor(ObjectConstructorInterface $constructor): self
    {
        $this->objectConstructor = $constructor;

        return $this;
    }

    public function setPropertyNamingStrategy(PropertyNamingStrategyInterface $propertyNamingStrategy): self
    {
        $this->propertyNamingStrategy = $propertyNamingStrategy;

        return $this;
    }

    public function setSerializationVisitor(string $format, SerializationVisitorFactory $visitor): self
    {
        $this->visitorsAdded = true;
        $this->serializationVisitors[$format] = $visitor;

        return $this;
    }

    public function setDeserializationVisitor(string $format, DeserializationVisitorFactory $visitor): self
    {
        $this->visitorsAdded = true;
        $this->deserializationVisitors[$format] = $visitor;

        return $this;
    }

    public function addDefaultSerializationVisitors(): self
    {
        $this->visitorsAdded = true;
        $this->serializationVisitors = [
            'xml' => new XmlSerializationVisitorFactory(),
            'json' => new JsonSerializationVisitorFactory(),
        ];

        return $this;
    }

    public function addDefaultDeserializationVisitors(): self
    {
        $this->visitorsAdded = true;
        $this->deserializationVisitors = [
            'xml' => new XmlDeserializationVisitorFactory(),
            'json' => new JsonDeserializationVisitorFactory(),
        ];

        return $this;
    }

    /**
     * @param bool $include Whether to include the metadata from the interfaces
     *
     * @return SerializerBuilder
     */
    public function includeInterfaceMetadata(bool $include): self
    {
        $this->includeInterfaceMetadata = $include;

        return $this;
    }

    /**
     * Sets a map of namespace prefixes to directories.
     *
     * This method overrides any previously defined directories.
     *
     * @param array <string,string> $namespacePrefixToDirMap
     *
     * @return SerializerBuilder
     *
     * @throws InvalidArgumentException When a directory does not exist.
     */
    public function setMetadataDirs(array $namespacePrefixToDirMap): self
    {
        foreach ($namespacePrefixToDirMap as $dir) {
            if (!is_dir($dir)) {
                throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
            }
        }

        $this->metadataDirs = $namespacePrefixToDirMap;

        return $this;
    }

    /**
     * Adds a directory where the serializer will look for class metadata.
     *
     * The namespace prefix will make the names of the actual metadata files a bit shorter. For example, let's assume
     * that you have a directory where you only store metadata files for the ``MyApplication\Entity`` namespace.
     *
     * If you use an empty prefix, your metadata files would need to look like:
     *
     * ``my-dir/MyApplication.Entity.SomeObject.yml``
     * ``my-dir/MyApplication.Entity.OtherObject.xml``
     *
     * If you use ``MyApplication\Entity`` as prefix, your metadata files would need to look like:
     *
     * ``my-dir/SomeObject.yml``
     * ``my-dir/OtherObject.yml``
     *
     * Please keep in mind that you currently may only have one directory per namespace prefix.
     *
     * @param string $dir             The directory where metadata files are located.
     * @param string $namespacePrefix An optional prefix if you only store metadata for specific namespaces in this directory.
     *
     * @return SerializerBuilder
     *
     * @throws InvalidArgumentException When a directory does not exist.
     * @throws InvalidArgumentException When a directory has already been registered.
     */
    public function addMetadataDir(string $dir, string $namespacePrefix = ''): self
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (isset($this->metadataDirs[$namespacePrefix])) {
            throw new InvalidArgumentException(sprintf('There is already a directory configured for the namespace prefix "%s". Please use replaceMetadataDir() to override directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    /**
     * Adds a map of namespace prefixes to directories.
     *
     * @param array <string,string> $namespacePrefixToDirMap
     *
     * @return SerializerBuilder
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap): self
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }

        return $this;
    }

    /**
     * Similar to addMetadataDir(), but overrides an existing entry.
     *
     * @return SerializerBuilder
     *
     * @throws InvalidArgumentException When a directory does not exist.
     * @throws InvalidArgumentException When no directory is configured for the ns prefix.
     */
    public function replaceMetadataDir(string $dir, string $namespacePrefix = ''): self
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (!isset($this->metadataDirs[$namespacePrefix])) {
            throw new InvalidArgumentException(sprintf('There is no directory configured for namespace prefix "%s". Please use addMetadataDir() for adding new directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    public function setMetadataDriverFactory(DriverFactoryInterface $driverFactory): self
    {
        $this->driverFactory = $driverFactory;

        return $this;
    }

    /**
     * @param SerializationContextFactoryInterface|callable $serializationContextFactory
     */
    public function setSerializationContextFactory($serializationContextFactory): self
    {
        if ($serializationContextFactory instanceof SerializationContextFactoryInterface) {
            $this->serializationContextFactory = $serializationContextFactory;
        } elseif (is_callable($serializationContextFactory)) {
            $this->serializationContextFactory = new CallableSerializationContextFactory(
                $serializationContextFactory
            );
        } else {
            throw new InvalidArgumentException('expected SerializationContextFactoryInterface or callable.');
        }

        return $this;
    }

    /**
     * @param DeserializationContextFactoryInterface|callable $deserializationContextFactory
     */
    public function setDeserializationContextFactory($deserializationContextFactory): self
    {
        if ($deserializationContextFactory instanceof DeserializationContextFactoryInterface) {
            $this->deserializationContextFactory = $deserializationContextFactory;
        } elseif (is_callable($deserializationContextFactory)) {
            $this->deserializationContextFactory = new CallableDeserializationContextFactory(
                $deserializationContextFactory
            );
        } else {
            throw new InvalidArgumentException('expected DeserializationContextFactoryInterface or callable.');
        }

        return $this;
    }

    public function setMetadataCache(CacheInterface $cache): self
    {
        $this->metadataCache = $cache;

        return $this;
    }

    public function setDocBlockTypeResolver(bool $docBlockTypeResolver): self
    {
        $this->docBlockTyperResolver = $docBlockTypeResolver;

        return $this;
    }

    public function build(): Serializer
    {
        $annotationReader = $this->annotationReader;
        if (null === $annotationReader) {
            $annotationReader = new AnnotationReader();
            $annotationReader = $this->decorateAnnotationReader($annotationReader);
        }

        if (null === $this->driverFactory) {
            $this->initializePropertyNamingStrategy();
            $this->driverFactory = new DefaultDriverFactory(
                $this->propertyNamingStrategy,
                $this->typeParser,
                $this->expressionEvaluator instanceof CompilableExpressionEvaluatorInterface ? $this->expressionEvaluator : null
            );
        }

        if ($this->docBlockTyperResolver) {
            $this->driverFactory = new DocBlockDriverFactory($this->driverFactory, $this->typeParser);
        }

        $metadataDriver = $this->driverFactory->createDriver($this->metadataDirs, $annotationReader);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);

        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->metadataCache) {
            $metadataFactory->setCache($this->metadataCache);
        } elseif (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir . '/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir . '/metadata'));
        }

        if (!$this->handlersConfigured) {
            $this->addDefaultHandlers();
        }

        if (!$this->listenersConfigured) {
            $this->addDefaultListeners();
        }

        if (!$this->visitorsAdded) {
            $this->addDefaultSerializationVisitors();
            $this->addDefaultDeserializationVisitors();
        }

        $navigatorFactories = [
            GraphNavigatorInterface::DIRECTION_SERIALIZATION => $this->getSerializationNavigatorFactory($metadataFactory),
            GraphNavigatorInterface::DIRECTION_DESERIALIZATION => $this->getDeserializationNavigatorFactory($metadataFactory),
        ];

        return new Serializer(
            $metadataFactory,
            $navigatorFactories,
            $this->serializationVisitors,
            $this->deserializationVisitors,
            $this->serializationContextFactory,
            $this->deserializationContextFactory,
            $this->typeParser
        );
    }

    private function getSerializationNavigatorFactory(MetadataFactoryInterface $metadataFactory): GraphNavigatorFactoryInterface
    {
        return new SerializationGraphNavigatorFactory(
            $metadataFactory,
            $this->handlerRegistry,
            $this->getAccessorStrategy(),
            $this->eventDispatcher,
            $this->expressionEvaluator
        );
    }

    private function getDeserializationNavigatorFactory(MetadataFactoryInterface $metadataFactory): GraphNavigatorFactoryInterface
    {
        return new DeserializationGraphNavigatorFactory(
            $metadataFactory,
            $this->handlerRegistry,
            $this->objectConstructor ?: new UnserializeObjectConstructor(),
            $this->getAccessorStrategy(),
            $this->eventDispatcher,
            $this->expressionEvaluator
        );
    }

    private function initializePropertyNamingStrategy(): void
    {
        if (null !== $this->propertyNamingStrategy) {
            return;
        }

        $this->propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
    }

    private function createDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (false === @mkdir($dir, 0777, true) && false === is_dir($dir)) {
            throw new RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }

    private function decorateAnnotationReader(Reader $annotationReader): Reader
    {
        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir . '/annotations');
            if (class_exists(FilesystemAdapter::class)) {
                $annotationsCache = new FilesystemAdapter('', 0, $this->cacheDir . '/annotations');
                $annotationReader = new PsrCachedReader($annotationReader, $annotationsCache, $this->debug);
            } else {
                $annotationsCache = new FilesystemCache($this->cacheDir . '/annotations');
                $annotationReader = new CachedReader($annotationReader, $annotationsCache, $this->debug);
            }
        }

        return $annotationReader;
    }
}
