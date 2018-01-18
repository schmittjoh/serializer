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
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Clazz;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Excursion;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Organization;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Person;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\School;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Student;
use JMS\Serializer\Tests\Fixtures\Doctrine\SingleTableInheritance\Teacher;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var ManagerRegistry */
    private $registry;

    /** @var Serializer */
    private $serializer;

    public function testDiscriminatorIsInferredForEntityBaseClass()
    {
        $school = new School();
        $json = $this->serializer->serialize($school, 'json');
        $this->assertEquals('{"type":"school"}', $json);

        $deserialized = $this->serializer->deserialize($json, Organization::class, 'json');
        $this->assertEquals($school, $deserialized);
    }

    public function testDiscriminatorIsInferredForGenericBaseClass()
    {
        $student = new Student();
        $json = $this->serializer->serialize($student, 'json');
        $this->assertEquals('{"type":"student"}', $json);

        $deserialized = $this->serializer->deserialize($json, Person::class, 'json');
        $this->assertEquals($student, $deserialized);
    }

    public function testDiscriminatorIsInferredFromDoctrine()
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        $student1 = new Student();
        $student2 = new Student();
        $teacher = new Teacher();
        $class = new Clazz($teacher, array($student1, $student2));

        $em->persist($student1);
        $em->persist($student2);
        $em->persist($teacher);
        $em->persist($class);
        $em->flush();
        $em->clear();

        $reloadedClass = $em->find(get_class($class), $class->getId());
        $this->assertNotSame($class, $reloadedClass);

        $json = $this->serializer->serialize($reloadedClass, 'json');
        $this->assertEquals('{"id":1,"teacher":{"id":1,"type":"teacher"},"students":[{"id":2,"type":"student"},{"id":3,"type":"student"}]}', $json);
    }

    protected function setUp()
    {
        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);

        $this->registry = $registry = new SimpleManagerRegistry(
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
            __DIR__ . '/../../Fixtures/Doctrine/SingleTableInheritance',
        )));
        $cfg->setAutoGenerateProxyClasses(true);
        $cfg->setProxyNamespace('JMS\Serializer\DoctrineProxy');
        $cfg->setProxyDir(sys_get_temp_dir() . '/serializer-test-proxies');

        $em = EntityManager::create($con, $cfg);

        return $em;
    }
}

class SimpleManagerRegistry extends AbstractManagerRegistry
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
