<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\Translator;

class FormErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \JMS\Serializer\Handler\FormErrorHandler
     */
    protected $handler;

    /**
     * @var \JMS\Serializer\VisitorInterface
     */
    protected $visitor;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $factory;

    public function setUp()
    {
        $this->handler = new FormErrorHandler(new Translator('en'));
        $this->visitor = new JsonSerializationVisitor(new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()));
        $this->dispatcher = new EventDispatcher();
        $this->factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
    }

    protected function tearDown()
    {
        $this->handler = null;
        $this->visitor = null;
        $this->dispatcher = null;
        $this->factory = null;
    }

    public function testSerializeEmptyFormError()
    {
        $form = $this->createForm();
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, array()));

        $this->assertSame('{}', $json);
    }

    public function testSerializeHasFormError()
    {
        $form = $this->createForm();
        $form->addError(new FormError('error!'));
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, array()));

        $this->assertSame(json_encode(array(
            'errors' => array(
                'error!',
            ),
        )), $json);
    }

    /**
     * @param string                   $name
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $dataClass
     *
     * @return FormBuilder
     */
    protected function getBuilder($name = 'name', EventDispatcherInterface $dispatcher = null, $dataClass = null)
    {
        return new FormBuilder($name, $dataClass, $dispatcher ?: $this->dispatcher, $this->factory);
    }

    /**
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForm($name = 'name')
    {
        $form = $this->getMock('Symfony\Component\Form\Test\FormInterface');
        $config = $this->getMock('Symfony\Component\Form\FormConfigInterface');

        $form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $form->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($config));

        return $form;
    }

    protected function createForm()
    {
        return $this->getBuilder()->getForm();
    }
}
