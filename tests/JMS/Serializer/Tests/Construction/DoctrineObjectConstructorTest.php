<?php
namespace JMS\Serializer\Tests\Construction;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use Metadata\MetadataFactory;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Construction\DoctrineObjectConstructor;

class DoctrineObjectConstructorTest extends \PHPUnit_Framework_TestCase
{
    protected $managerRegistryMock;
    protected $fallbackConstructorMock;
    protected $entityManagerMock;

    protected function setUp()
    {
        parent::setUp();

        $this->managerRegistryMock = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->disableOriginalConstructor()->getMock();
        $this->fallbackConstructorMock = $this->getMockBuilder('JMS\Serializer\Construction\UnserializeObjectConstructor')->disableOriginalConstructor()->getMock();
    }

    protected function getDoctrineObjectConstructor()
    {
        return new DoctrineObjectConstructor($this->managerRegistryMock, $this->fallbackConstructorMock);
    }


    protected function initObjectManager($isTransient)
    {
        $this->entityManagerMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $classMetadataFactory = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory')->disableOriginalConstructor()->getMock();

        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')->will($this->returnValue($this->entityManagerMock));
        $this->entityManagerMock->expects($this->any())->method('getMetadataFactory')->will($this->returnValue($classMetadataFactory));
        $classMetadataFactory->expects($this->any())->method('isTransient')->will($this->returnValue($isTransient));
    }


    public function testMissingObjectManager()
    {
        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')->will($this->returnValue(null));
        $this->fallbackConstructorMock->expects($this->once())->method('construct');

        $visitor = new JsonDeserializationVisitor(new CamelCaseNamingStrategy());
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Article');

        $this->getDoctrineObjectConstructor()->construct($visitor, $metadata, array(), array());
    }

    public function testTransientObject()
    {
        $this->initObjectManager(true);
        $this->fallbackConstructorMock->expects($this->once())->method('construct');

        $visitor = new JsonDeserializationVisitor(new CamelCaseNamingStrategy());
        $metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Article');

        $this->getDoctrineObjectConstructor()->construct($visitor, $metadata, array(), array());
    }

    public function testProxyLoad()
    {
        $this->initObjectManager(false);

        $this->entityManagerMock->expects($this->once())->method('getReference');

        $metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Article');
        $visitor = new JsonDeserializationVisitor(new CamelCaseNamingStrategy());

        $this->getDoctrineObjectConstructor()->construct($visitor, $metadata, 12, array());
    }

    public function providerFind()
    {
        return array(
                array(array('id' => 45), false, 'JMS\Serializer\Tests\Fixtures\Doctrine\Author', array('id' => 45), array('id'), new CamelCaseNamingStrategy()),
                array(array('idFirst' => 45, 'idSecond' => 78), false, 'JMS\Serializer\Tests\Fixtures\Doctrine\CompositePrimaryKey', array('id_first_serialized' => 45, 'id_second_serialized' => 78), array('idFirst', 'idSecond'), new CamelCaseNamingStrategy()),
                array(array(), true, 'JMS\Serializer\Tests\Fixtures\Doctrine\CompositePrimaryKey', array('id_first_serialized' => 45), array('idFirst', 'idSecond'), new CamelCaseNamingStrategy()),
                array(array(), true, 'JMS\Serializer\Tests\Fixtures\Doctrine\CompositePrimaryKey', array('id_first_serialized' => 45, 'id_second_serialized' => null), array('idFirst', 'idSecond'), new CamelCaseNamingStrategy()),
        );
    }

    /**
     * @dataProvider providerFind
     */
    public function testFind($expectedIdentifierList, $fallback, $entity, $data, $identifierFieldNames, $namingStrategy)
    {
        $this->initObjectManager(false);

        $classMetadataMock = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')->disableOriginalConstructor()->getMock();

        $this->entityManagerMock->expects($this->once())->method('getClassMetadata')->will($this->returnValue($classMetadataMock));
        $classMetadataMock->expects($this->once())->method('getIdentifierFieldNames')->will($this->returnValue($identifierFieldNames));

        $metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $metadata = $metadataFactory->getMetadataForClass($entity);

        $visitor = new JsonDeserializationVisitor($namingStrategy);

        if ($fallback) {
            $this->fallbackConstructorMock->expects($this->once())->method('construct');
        } else {
            $this->entityManagerMock->expects($this->once())->method('find')->with($metadata->name, $expectedIdentifierList);
        }

        $this->getDoctrineObjectConstructor()->construct($visitor, $metadata, $data, array());
    }
}