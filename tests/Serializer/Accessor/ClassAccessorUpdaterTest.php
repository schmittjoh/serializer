<?php

namespace JMS\Serializer\Tests\Serializer\Accessor;

use JMS\Serializer\Accessor\Updater\ClassAccessorUpdater;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

final class ClassAccessorUpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUpdateParameters
     *
     * @param string $updaterType
     * @param string $updaterNaming
     * @param string $classType
     * @param string $classNaming
     * @param string $propertyType
     * @param string $propertyNaming
     * @param string $getter
     * @param string $setter
     * @param array $getterPrefixes
     * @param array $setterPrefixes
     */
    public function testUpdateParameters(
        $updaterType,
        $updaterNaming,
        $classType,
        $classNaming,
        $propertyType,
        $propertyNaming,
        $getter,
        $setter,
        $getterPrefixes = ['get'],
        $setterPrefixes = ['set']
    )
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $propertyMetadata = $this->createMock(PropertyMetadata::class);
        $classMetadata->propertyMetadata = [$propertyMetadata];

        $propertyMetadata->name = 'foo_bar';

        $classMetadata->accessType = $classType;
        $classMetadata->accessTypeNaming = $classNaming;
        $propertyMetadata->accessType = $propertyType;
        $propertyMetadata->accessTypeNaming = $propertyNaming;

        $propertyReflection = $this->createMock(\ReflectionProperty::class);
        $classReflection = $this->createMock(\ReflectionClass::class);
        $methodReflection = $this->createMock(\ReflectionMethod::class);
        $propertyMetadata->method('getReflection')->willReturn($propertyReflection);
        $propertyReflection->method('getDeclaringClass')->willReturn($classReflection);
        $classReflection->method('hasMethod')->willReturn(true);
        $classReflection->method('getMethod')->willReturn($methodReflection);
        $methodReflection->method('isPublic')->willReturn(true);

        (new ClassAccessorUpdater(
            $updaterType,
            $updaterNaming,
            $getterPrefixes,
            $setterPrefixes
        ))->update($classMetadata);
        $this->assertSame($getter, $propertyMetadata->getter);
        $this->assertSame($setter, $propertyMetadata->setter);
    }

    public function testUpdateDoesNothingForProperties()
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $propertyMetadata = $this->createMock(PropertyMetadata::class);
        $classMetadata->propertyMetadata = [$propertyMetadata];

        $propertyMetadata->accessType = PropertyMetadata::ACCESS_TYPE_PROPERTY;

        (new ClassAccessorUpdater())->update($classMetadata);
        $this->assertNull($propertyMetadata->getter);
        $this->assertNull($propertyMetadata->setter);
    }

    /**
     * @expectedException \JMS\Serializer\Exception\RuntimeException
     * @expectedExceptionMessage Undefined naming type 'wrong'.
     */
    public function testUpdateWrongNaming()
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $propertyMetadata = $this->createMock(PropertyMetadata::class);
        $classMetadata->propertyMetadata = [$propertyMetadata];

        $propertyReflection = $this->createMock(\ReflectionProperty::class);
        $classReflection = $this->createMock(\ReflectionClass::class);
        $propertyMetadata->method('getReflection')->willReturn($propertyReflection);
        $propertyReflection->method('getDeclaringClass')->willReturn($classReflection);

        (new ClassAccessorUpdater(PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD, 'wrong'))->update($classMetadata);
    }

    /**
     * @expectedException \JMS\Serializer\Exception\RuntimeException
     * @expectedExceptionMessage Specify public setter
     */
    public function testUpdateNoSetter()
    {

        $classMetadata = $this->createMock(ClassMetadata::class);
        $propertyMetadata = $this->createMock(PropertyMetadata::class);
        $classMetadata->propertyMetadata = [$propertyMetadata];

        $propertyReflection = $this->createMock(\ReflectionProperty::class);
        $classReflection = $this->createMock(\ReflectionClass::class);
        $propertyMetadata->method('getReflection')->willReturn($propertyReflection);
        $propertyReflection->method('getDeclaringClass')->willReturn($classReflection);
        $classReflection->method('hasMethod')->willReturn(false);

        (new ClassAccessorUpdater(
            PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
            PropertyMetadata::ACCESS_TYPE_NAMING_EXACT
        ))->update($classMetadata);
    }

    /**
     * @dataProvider provideUpdateErrors
     *
     * @expectedException \JMS\Serializer\Exception\RuntimeException
     *
     * @param bool $hasSetterMethod
     * @param bool $hasGetterMethod
     * @param bool $isPublicSetter
     * @param bool $isPublicGetter
     * @param bool $isReadOnly
     * @param $expectedMessage
     */
    public function testUpdateErrors(
        $hasSetterMethod,
        $hasGetterMethod,
        $isPublicSetter,
        $isPublicGetter,
        $isReadOnly,
        $expectedMessage
    )
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $propertyMetadata = $this->createMock(PropertyMetadata::class);
        $classMetadata->propertyMetadata = [$propertyMetadata];
        $propertyMetadata->readOnly = $isReadOnly;

        $propertyReflection = $this->createMock(\ReflectionProperty::class);
        $classReflection = $this->createMock(\ReflectionClass::class);
        $methodReflection = $this->createMock(\ReflectionMethod::class);
        $propertyMetadata->method('getReflection')->willReturn($propertyReflection);
        $propertyReflection->method('getDeclaringClass')->willReturn($classReflection);
        $classReflection->method('hasMethod')->willReturnOnConsecutiveCalls($hasSetterMethod, $hasGetterMethod);
        $classReflection->method('getMethod')->willReturn($methodReflection);
        $methodReflection->method('isPublic')->willReturnOnConsecutiveCalls($isPublicSetter, $isPublicGetter);

        $this->expectExceptionMessage($expectedMessage);

        (new ClassAccessorUpdater(
            PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
            PropertyMetadata::ACCESS_TYPE_NAMING_EXACT
        ))->update($classMetadata);
    }

    public function provideUpdateParameters()
    {
        return [
            'exact defaults' => [
                'updaterType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'updaterNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_EXACT,
                'classType' => null,
                'classNaming' => null,
                'propertyType' => null,
                'propertyNaming' => null,
                'getter' => 'getfoo_bar',
                'setter' => 'setfoo_bar',
            ],
            'camel class' => [
                'updaterType' => null,
                'updaterNaming' => null,
                'classType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'classNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE,
                'propertyType' => null,
                'propertyNaming' => null,
                'getter' => 'getfoobar',
                'setter' => 'setfoobar',
            ],
            'exact property' => [
                'updaterType' => null,
                'updaterNaming' => null,
                'classType' => null,
                'classNaming' => null,
                'propertyType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'propertyNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_EXACT,
                'getter' => 'getfoo_bar',
                'setter' => 'setfoo_bar',
            ],
            'property over class' => [
                'updaterType' => PropertyMetadata::ACCESS_TYPE_PROPERTY,
                'updaterNaming' => null,
                'classType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'classNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE,
                'propertyType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'propertyNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_EXACT,
                'getter' => 'getfoo_bar',
                'setter' => 'setfoo_bar',
            ],
            'class over updater' => [
                'updaterType' => PropertyMetadata::ACCESS_TYPE_PROPERTY,
                'updaterNaming' => null,
                'classType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'classNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE,
                'propertyType' => null,
                'propertyNaming' => null,
                'getter' => 'getfoobar',
                'setter' => 'setfoobar',
            ],
            'custom prefixes' => [
                'updaterType' => null,
                'updaterNaming' => null,
                'classType' => null,
                'classNaming' => null,
                'propertyType' => PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD,
                'propertyNaming' => PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE,
                'getter' => 'isfoobar',
                'setter' => 'updatefoobar',
                'getterPrefixes' => ['is'],
                'setterPrefixes' => ['update'],
            ],
        ];
    }

    public function provideUpdateErrors()
    {
        return [
            'no setter' => [
                'hasSetterMethod' => false,
                'hasGetterMethod' => null,
                'isPublicSetter' => null,
                'isPublicGetter' => null,
                'isReadOnly' => false,
                'expectedMessage' => 'Specify public setter',
            ],
            'no public setter' => [
                'hasSetterMethod' => true,
                'hasGetterMethod' => null,
                'isPublicSetter' => false,
                'isPublicGetter' => null,
                'isReadOnly' => false,
                'expectedMessage' => 'Specify public setter',
            ],
            'no getter' => [
                'hasSetterMethod' => true,
                'hasGetterMethod' => false,
                'isPublicSetter' => true,
                'isPublicGetter' => null,
                'isReadOnly' => false,
                'expectedMessage' => 'Specify public getter',
            ],
            'no public getter' => [
                'hasSetterMethod' => true,
                'hasGetterMethod' => true,
                'isPublicSetter' => true,
                'isPublicGetter' => false,
                'isReadOnly' => false,
                'expectedMessage' => 'Specify public getter',
            ],
            'no setter for read-only' => [
                'hasSetterMethod' => null,
                'hasGetterMethod' => false,
                'isPublicSetter' => null,
                'isPublicGetter' => null,
                'isReadOnly' => true,
                'expectedMessage' => 'Specify public getter',
            ]
        ];
    }

    protected function createMock($originalClassName)
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();
    }
}
