<?php

namespace JMS\Serializer\Tests\Serializer\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use JMS\Serializer\Builder\CallbackDriverFactory;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Construction\DoctrineObjectConstructor;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Doctrine\Author;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Excursion;
use JMS\Serializer\VisitorInterface;

class ObjectConstructorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ManagerRegistry */
    private $registry;

    /** @var Serializer */
    private $serializer;

    /** @var VisitorInterface */
    private $visitor;

    /** @var DeserializationContext */
    private $context;

    public function testFindEntity()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();
        $em->clear();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);

        $this->assertEquals($author, $authorFetched);
    }

    public function testFindManagedEntity()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);

        $this->assertSame($author, $authorFetched);
    }

    public function testMissingAuthor()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback);
        $author = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        $this->assertNull($author);
    }

    public function testMissingAuthorFallback()
    {
        $author = new Author('John');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        $this->assertSame($author, $authorFetched);
    }

    public function testMissingNotManaged()
    {
        $author = new \JMS\Serializer\Tests\Fixtures\DoctrinePHPCR\Author('foo');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
        $this->assertSame($author, $authorFetched);
    }

    public function testReference()
    {
        $em = $this->registry->getManager();

        $author = new Author('John', 5);
        $em->persist($author);
        $em->flush();

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_FALLBACK);
        $authorFetched = $constructor->construct($this->visitor, $class, 5, $type, $this->context);
        $this->assertSame($author, $authorFetched);
    }

    /**
     * @expectedException \JMS\Serializer\Exception\ObjectConstructionException
     */
    public function testMissingAuthorException()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, DoctrineObjectConstructor::ON_MISSING_EXCEPTION);
        $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
    }

    /**
     * @expectedException \JMS\Serializer\Exception\InvalidArgumentException
     */
    public function testInvalidArg()
    {
        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, 'foo');
        $constructor->construct($this->visitor, $class, ['id' => 5], $type, $this->context);
    }

    public function testMissingData()
    {
        $author = new Author('John');

        $fallback = $this->getMockBuilder(ObjectConstructorInterface::class)->getMock();
        $fallback->expects($this->once())->method('construct')->willReturn($author);

        $type = array('name' => Author::class, 'params' => array());
        $class = new ClassMetadata(Author::class);

        $constructor = new DoctrineObjectConstructor($this->registry, $fallback, 'foo');
        $authorFetched = $constructor->construct($this->visitor, $class, ['foo' => 5], $type, $this->context);
        $this->assertSame($author, $authorFetched);
    }

    protected function setUp()
    {
        $this->visitor = $this->getMockBuilder('JMS\Serializer\VisitorInterface')->getMock();
        $this->context = $this->getMockBuilder('JMS\Serializer\DeserializationContext')->getMock();

        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);

        $this->registry = $registry = new SimpleBaseManagerRegistry(
            function ($id) use ($connection, $entityManager) {
                switch ($id) {
                    case 'default_connection':
                        return $connection;

                    case 'default_manager':
                        return $entityManager;

                    default:
                        throw new \RuntimeException(sprintf('Unknown service id "%s".', $id));
                }
            }
        );

        $this->serializer = SerializerBuilder::create()
            ->setMetadataDriverFactory(new CallbackDriverFactory(
                function (array $metadataDirs, Reader $annotationReader) use ($registry) {
                    $defaultFactory = new DefaultDriverFactory();

                    return new DoctrineTypeDriver($defaultFactory->createDriver($metadataDirs, $annotationReader), $registry);
                }
            ))
            ->build();

        $this->prepareDatabase();
    }

    private function prepareDatabase()
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    private function createConnection()
    {
        $con = DriverManager::getConnection(array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ));

        return $con;
    }

    private function createEntityManager(Connection $con)
    {
        $cfg = new Configuration();
        $cfg->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader(), array(
            __DIR__ . '/../../Fixtures/Doctrine',
        )));
        $cfg->setAutoGenerateProxyClasses(true);
        $cfg->setProxyNamespace('JMS\Serializer\DoctrineProxy');
        $cfg->setProxyDir(sys_get_temp_dir() . '/serializer-test-proxies');

        $em = EntityManager::create($con, $cfg);

        return $em;
    }
}

\Doctrine\DBAL\Types\Type::addType('Author', 'Doctrine\DBAL\Types\StringType');
\Doctrine\DBAL\Types\Type::addType('some_custom_type', 'Doctrine\DBAL\Types\StringType');

class SimpleBaseManagerRegistry extends AbstractManagerRegistry
{
    private $services = array();
    private $serviceCreator;

    public function __construct($serviceCreator, $name = 'anonymous', array $connections = array('default' => 'default_connection'), array $managers = array('default' => 'default_manager'), $defaultConnection = null, $defaultManager = null, $proxyInterface = 'Doctrine\Common\Persistence\Proxy')
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
                throw new \LogicException(sprintf('Unsupported manager type "%s".', get_class($manager)));
            }
        }

        throw new \RuntimeException(sprintf('The namespace alias "%s" is not known to any manager.', $alias));
    }
}
