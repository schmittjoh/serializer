<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Version as ORMVersion;
use Doctrine\Persistence\AbstractManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\Proxy;
use JMS\Serializer\Builder\CallbackDriverFactory;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Construction\DoctrineObjectConstructor;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\ObjectConstructionException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Tests\Fixtures\Doctrine\Embeddable\BlogPostSeo;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\Author;
use JMS\Serializer\Tests\Fixtures\Doctrine\Entity\AuthorExcludedId;
use JMS\Serializer\Tests\Fixtures\Doctrine\IdentityFields\Server;
use JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection\App;
use JMS\Serializer\Tests\Fixtures\Doctrine\PersistendCollection\SmartPhone;
use JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Author as DoctrinePHPCRAuthor;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use LogicException;
use Metadata\Driver\AdvancedDriverInterface;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use SimpleXMLElement;

use function assert;

class ObjectConstructorTest extends TestCase
{
    /** @var AbstractManagerRegistry */
    private $registry;

    /** @var Serializer */
    private $serializer;

    /** @var DeserializationVisitorInterface */
    private $visitor;

    /** @var DeserializationContext */
    private $context;

    /** @var AdvancedDriverInterface */
    private $driver;

    public function testFindEntity()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();
        $em->clear();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);

        self::assertEquals($author, $authorFetched);
    }

    public function testFindEntityExcludedByGroupsUsesFallback()
    {
        $graph = $this->createMock(GraphNavigatorInterface::class);
        $metadata = $this->createMock(MetadataFactoryInterface::class);

        $author = new Author('John');
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $context = DeserializationContext::create()->setGroups('foo');
        $context->initialize('json', $this->visitor, $graph, $metadata);
        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $context);

        self::assertSame($author, $authorFetched);
    }

    public function testFindEntityExcludedUsesFallback()
    {
        $author = new Author('John');
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = ['name' => AuthorExcludedId::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(AuthorExcludedId::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);

        self::assertSame($author, $authorFetched);
    }

    public function testFindManagedEntity()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);

        self::assertSame($author, $authorFetched);
    }

    public function testMissingAuthor()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $author = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        self::assertNull($author);
    }

    public function testMissingAuthorFallback()
    {
        $author = new Author('John');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        self::assertSame($author, $authorFetched);
    }

    public function testMissingNotManaged()
    {
        $author = new DoctrinePHPCRAuthor('foo');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        self::assertSame($author, $authorFetched);
    }

    public function testReference()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, 5, $type, $this->context);
        self::assertSame($author, $authorFetched);
    }

    public function testMissingAuthorException()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_EXCEPTION);

        $this->expectException(ObjectConstructionException::class);

        $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
    }

    public function testInvalidArg()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, 'foo');

        $this->expectException(InvalidArgumentException::class);

        $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
    }

    public function testMissingData()
    {
        $author = new Author('John');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = ['name' => Author::class, 'params' => []];
        $class = $this->driver->loadMetadataForClass(new ReflectionClass(Author::class));

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, 'foo');
        $authorFetched = $constructor->construct($this->visitor, $class, ['foo' => 5], $type, $this->context);
        self::assertSame($author, $authorFetched);
    }

    public function testNamingForIdentifierColumnIsConsidered()
    {
        $serializer = $this->createSerializerWithDoctrineObjectConstructor();

        $em = $this->registry->getManager();
        assert($em instanceof EntityManager);
        $server = new Server('Linux', '127.0.0.1', 'home');
        $em->persist($server);
        $em->flush();
        $em->clear();

        $jsonData = '{"ip_address":"127.0.0.1", "server_id_extracted":"home", "name":"Windows"}';
        $serverDeserialized = $serializer->deserialize($jsonData, Server::class, 'json');
        assert($serverDeserialized instanceof Server);

        self::assertSame(
            $em->getUnitOfWork()->getEntityState($serverDeserialized),
            UnitOfWork::STATE_MANAGED
        );
    }

    public function dataProviderPersistendCollectionIsNotReplaced(): array
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <result>
                  <id><![CDATA[Renes UPhone SX]]></id>
                  <name><![CDATA[uPhone]]></name>
                  <applications>
                    <entry>
                      <id><![CDATA[002]]></id>
                      <name><![CDATA[FlappyBird]]></name>
                    </entry>
                  </applications>
                </result>';

        return [
            [
                'serializedData' => '{"id":"Renes UPhone SX", "applications":[{"id":"002", "name":"FlappyBird"}]}',
                'type' => 'json',
            ],
            [
                'serializedData' => $xml,
                'type' => 'xml',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderPersistendCollectionIsNotReplaced
     */
    public function testPersistendCollectionIsNotReplaced(string $data, string $type): void
    {
        $serializer = $this->createSerializerWithDoctrineObjectConstructor();

        $em = $this->registry->getManager();
        assert($em instanceof EntityManager);
        $smartPhone = new SmartPhone('uPhone', 'Renes UPhone SX');
        $angryFlyingThings = new App('001', 'Angry flying things 3', $smartPhone);
        $flappyFlyingThing = new App('002', 'Flappy flying thing', $smartPhone);

        $smartPhone->addApp($angryFlyingThings);
        $smartPhone->addApp($flappyFlyingThing);

        $em->persist($smartPhone);
        $em->flush();
        $em->clear();

        $smartPhoneDeserialized = $serializer->deserialize($data, SmartPhone::class, $type);
        self::assertInstanceOf(SmartPhone::class, $smartPhoneDeserialized);

        self::assertSame(
            $em->getUnitOfWork()->getEntityState($smartPhoneDeserialized),
            UnitOfWork::STATE_MANAGED
        );

        self::assertInstanceOf(PersistentCollection::class, $smartPhoneDeserialized->getAppsRaw());
        self::assertNotEmpty($smartPhoneDeserialized->getApps());
        self::assertCount(1, $smartPhoneDeserialized->getApps());
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', 'FlappyBird'));
        self::assertCount(
            1,
            $smartPhoneDeserialized->getApps(
                $criteria
            )
        );
        $firstApp = $smartPhoneDeserialized->getApps()->first();

        self::assertSame(
            $em->getUnitOfWork()->getEntityState($firstApp),
            UnitOfWork::STATE_MANAGED
        );

        $em->flush();
        $em->clear();

        $smartPhone = $em->find(SmartPhone::class, 'Renes UPhone SX');
        assert($smartPhone instanceof SmartPhone);
        static::assertCount(1, $smartPhone->getApps());
        static::assertEquals('FlappyBird', $smartPhone->getApps()->first()->getName());
    }

    public function testFallbackOnEmbeddableClassWithXmlDriver()
    {
        if (ORMVersion::compare('2.5') >= 0) {
            $this->markTestSkipped('Not using Doctrine ORM >= 2.5 with Embedded entities');
        }

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct');

        $connection = $this->createConnection();
        $entityManager = $this->createXmlEntityManager($connection);

        $this->registry = $registry = new SimpleBaseManagerRegistry(
            static function ($id) use ($connection, $entityManager) {
                switch ($id) {
                    case 'default_connection':
                        return $connection;

                    case 'default_manager':
                        return $entityManager;

                    default:
                        throw new RuntimeException(sprintf('Unknown service id "%s".', $id));
                }
            }
        );

        $type = ['name' => BlogPostSeo::class, 'params' => []];
        $class = new ClassMetadata(BlogPostSeo::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $constructor->construct($this->visitor, $class, ['metaTitle' => 'test'], $type, $this->context);
    }

    public function testFallbackOnEmbeddableClassWithXmlDriverAndXmlData()
    {
        if (ORMVersion::compare('2.5') >= 0) {
            $this->markTestSkipped('Not using Doctrine ORM >= 2.5 with Embedded entities');
        }

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct');

        $connection = $this->createConnection();
        $entityManager = $this->createXmlEntityManager($connection);

        $this->registry = $registry = new SimpleBaseManagerRegistry(
            static function ($id) use ($connection, $entityManager) {
                switch ($id) {
                    case 'default_connection':
                        return $connection;

                    case 'default_manager':
                        return $entityManager;

                    default:
                        throw new RuntimeException(sprintf('Unknown service id "%s".', $id));
                }
            }
        );

        $type = ['name' => BlogPostSeo::class, 'params' => []];
        $class = new ClassMetadata(BlogPostSeo::class);

        $data = new SimpleXMLElement('<metaTitle>test</metaTitle>');
        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $constructor->construct($this->visitor, $class, $data, $type, $this->context);
    }

    /**
     * php7.4 Using array_key_exists() on objects is deprecated.
     */
    public function testArrayKeyExistsOnObject(): void
    {
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $ormClassMetadata = $this->createMock(DoctrineClassMetadata::class);
        $ormClassMetadata
            ->expects(self::atLeastOnce())
            ->method('getIdentifierFieldNames')
            ->willReturn(['id']);

        $om = $this->createMock(ObjectManager::class);
        $om
            ->expects(self::atLeastOnce())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory);
        $om
            ->expects(self::atLeastOnce())
            ->method('getClassMetadata')
            ->willReturnMap([
                [BlogPostSeo::class, $ormClassMetadata],
            ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects(self::atLeastOnce())
            ->method('getManagerForClass')
            ->willReturnMap([
                [BlogPostSeo::class, $om],
            ]);

        $fallback = $this->createMock(ObjectConstructorInterface::class);
        $fallback
            ->expects(self::once())
            ->method('construct');

        $type = ['name' => BlogPostSeo::class, 'params' => []];

        $pm = $this->createMock(PropertyMetadata::class);
        $pm->serializedName = 'id';

        $classMetadata = new ClassMetadata(BlogPostSeo::class);
        $classMetadata->propertyMetadata['id'] = $pm;

        $data = new SimpleXMLElement('<metaTitle>test</metaTitle>');
        $constructor = new DoctrineObjectConstructor($registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $constructor->construct($this->visitor, $classMetadata, $data, $type, $this->context);
    }

    protected function setUp(): void
    {
        $this->visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $this->context = $this->getMockBuilder('JMS\Serializer\DeserializationContext')->getMock();

        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);

        $this->registry = $registry = new SimpleBaseManagerRegistry(
            static function ($id) use ($connection, $entityManager) {
                switch ($id) {
                    case 'default_connection':
                        return $connection;

                    case 'default_manager':
                        return $entityManager;

                    default:
                        throw new RuntimeException(sprintf('Unknown service id "%s".', $id));
                }
            }
        );
        $driver = null;
        $this->driver = &$driver;
        $this->serializer = SerializerBuilder::create()
            ->setMetadataDriverFactory(new CallbackDriverFactory(
                static function (array $metadataDirs, Reader $annotationReader) use ($registry, &$driver) {
                    $defaultFactory = new DefaultDriverFactory(new IdenticalPropertyNamingStrategy());

                    return $driver = new DoctrineTypeDriver($defaultFactory->createDriver($metadataDirs, $annotationReader), $registry);
                }
            ))
            ->build();

        $this->prepareDatabase();
    }

    private function prepareDatabase()
    {
        $em = $this->registry->getManager();
        assert($em instanceof EntityManager);

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    private function createConnection()
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);
    }

    private function createEntityManager(Connection $con, ?Configuration $cfg = null)
    {
        if (!$cfg) {
            $cfg = new Configuration();
            $cfg->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader(), [
                __DIR__ . '/../../Fixtures/Doctrine/Entity',
                __DIR__ . '/../../Fixtures/Doctrine/IdentityFields',
                __DIR__ . '/../../Fixtures/Doctrine/PersistendCollection',
            ]));
        }

        $cfg->setAutoGenerateProxyClasses(true);
        $cfg->setProxyNamespace('JMS\Serializer\DoctrineProxy');
        $cfg->setProxyDir(sys_get_temp_dir() . '/serializer-test-proxies');

        return EntityManager::create($con, $cfg);
    }

    /**
     * @param Connection $con
     *
     * @return EntityManager
     */
    private function createXmlEntityManager(Connection $con)
    {
        $cfg = new Configuration();
        $cfg->setMetadataDriverImpl(new XmlDriver([
            __DIR__ . '/../../Fixtures/Doctrine/XmlMapping',
        ]));

        return $this->createEntityManager($con, $cfg);
    }

    /**
     * @return SerializerInterface
     */
    private function createSerializerWithDoctrineObjectConstructor()
    {
        return SerializerBuilder::create()
            ->setObjectConstructor(
                new DoctrineObjectConstructor(
                    $this->registry,
                    new UnserializeObjectConstructor(),
                    DoctrineObjectConstructor::ON_MISSING_FALLBACK
                )
            )
            ->addDefaultHandlers()
            ->configureHandlers(function (HandlerRegistryInterface $handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(
                    new ArrayCollectionHandler(
                        true,
                        $this->registry
                    )
                );
            })
            ->build();
    }
}

Type::addType('Author', 'Doctrine\DBAL\Types\StringType');
Type::addType('some_custom_type', 'Doctrine\DBAL\Types\StringType');

class SimpleBaseManagerRegistry extends AbstractManagerRegistry
{
    private $services = [];
    private $serviceCreator;

    public function __construct($serviceCreator, $name = 'anonymous', array $connections = ['default' => 'default_connection'], array $managers = ['default' => 'default_manager'], $defaultConnection = null, $defaultManager = null, $proxyInterface = Proxy::class)
    {
        if (null === $defaultConnection) {
            $defaultConnection = key($connections);
        }

        if (null === $defaultManager) {
            $defaultManager = key($managers);
        }

        parent::__construct($name, $connections, $managers, $defaultConnection, $defaultManager, $proxyInterface);

        if (!is_callable($serviceCreator)) {
            throw new \InvalidArgumentException('$serviceCreator must be a valid callable.');
        }

        $this->serviceCreator = $serviceCreator;
    }

    public function getService($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        return $this->services[$name] = call_user_func($this->serviceCreator, $name);
    }

    public function resetService($name)
    {
        unset($this->services[$name]);
    }

    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            $manager = $this->getManager($name);

            if ($manager instanceof EntityManager) {
                try {
                    return $manager->getConfiguration()->getEntityNamespace($alias);
                } catch (ORMException $ex) {
                    // Probably mapped by another entity manager, or invalid, just ignore this here.
                }
            } else {
                throw new LogicException(sprintf('Unsupported manager type "%s".', get_class($manager)));
            }
        }

        throw new RuntimeException(sprintf('The namespace alias "%s" is not known to any manager.', $alias));
    }
}
