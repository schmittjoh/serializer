<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Forms;
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
        $this->factory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
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

    public function testErrorHandlerWithoutTranslator()
    {
        $this->handler = new FormErrorHandler();
        $form = $this->createForm();
        $form->addError(new FormError('error!'));
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, array()));

        $this->assertSame(json_encode(array(
            'errors' => array(
                'error!',
            ),
        )), $json);
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


    public function testSerializeChildElements()
    {
        $formFactory = Forms::createFormFactory();
        $form = $formFactory->createBuilder()
            ->add('child')
            ->add('date')
            ->getForm();

        $form->addError(new FormError('error!'));
        $form->get('date')->addError(new FormError('child-error'));

        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, array()));

        $this->assertSame(json_encode(array(
            'errors' => array(
                'error!',
            ),
            'children' => [
                'child' => new \stdClass(),
                'date' => ['errors' => ['child-error']]
            ]
        )), $json);

    }

    public function testDefaultTranslationDomain()
    {
        /** @var Translator|\PHPUnit_Framework_MockObject_MockObject $translator */
        $translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')->getMock();

        $handler = new FormErrorHandler($translator);

        $translator->expects($this->once())
            ->method('trans')
            ->with(
                $this->equalTo('error!'),
                $this->equalTo([]),
                $this->equalTo('validators')
            );

        $formError = $this->getMockBuilder('Symfony\Component\Form\FormError')->disableOriginalConstructor()->getMock();
        $formError->expects($this->once())->method('getMessageTemplate')->willReturn('error!');
        $formError->expects($this->once())->method('getMessagePluralization')->willReturn(null);
        $formError->expects($this->once())->method('getMessageParameters')->willReturn([]);

        $this->invokeMethod($handler, 'getErrorMessage', [$formError,]);
    }

    public function testDefaultTranslationDomainWithPluralTranslation()
    {
        /** @var Translator|\PHPUnit_Framework_MockObject_MockObject $translator */
        $translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')->getMock();

        $handler = new FormErrorHandler($translator);

        $translator->expects($this->once())
            ->method('transChoice')
            ->with(
                $this->equalTo('error!'),
                $this->equalTo(0),
                $this->equalTo([]),
                $this->equalTo('validators')
            );

        $formError = $this->getMockBuilder('Symfony\Component\Form\FormError')->disableOriginalConstructor()->getMock();
        $formError->expects($this->once())->method('getMessageTemplate')->willReturn('error!');
        $formError->expects($this->exactly(2))->method('getMessagePluralization')->willReturn(0);
        $formError->expects($this->once())->method('getMessageParameters')->willReturn([]);

        $this->invokeMethod($handler, 'getErrorMessage', [$formError,]);
    }

    public function testCustomTranslationDomain()
    {
        /** @var Translator|\PHPUnit_Framework_MockObject_MockObject $translator */
        $translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')->getMock();

        $handler = new FormErrorHandler($translator, 'custom_domain');

        $translator->expects($this->once())
            ->method('trans')
            ->with(
                $this->equalTo('error!'),
                $this->equalTo([]),
                $this->equalTo('custom_domain')
            );

        $formError = $this->getMockBuilder('Symfony\Component\Form\FormError')->disableOriginalConstructor()->getMock();
        $formError->expects($this->once())->method('getMessageTemplate')->willReturn('error!');
        $formError->expects($this->once())->method('getMessagePluralization')->willReturn(null);
        $formError->expects($this->once())->method('getMessageParameters')->willReturn([]);

        $this->invokeMethod($handler, 'getErrorMessage', [$formError,]);
    }

    public function testCustomTranslationDomainWithPluralTranslation()
    {
        /** @var Translator|\PHPUnit_Framework_MockObject_MockObject $translator */
        $translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')->getMock();

        $handler = new FormErrorHandler($translator, 'custom_domain');

        $translator->expects($this->once())
            ->method('transChoice')
            ->with(
                $this->equalTo('error!'),
                $this->equalTo(0),
                $this->equalTo([]),
                $this->equalTo('custom_domain')
            );

        $formError = $this->getMockBuilder('Symfony\Component\Form\FormError')->disableOriginalConstructor()->getMock();
        $formError->expects($this->once())->method('getMessageTemplate')->willReturn('error!');
        $formError->expects($this->exactly(2))->method('getMessagePluralization')->willReturn(0);
        $formError->expects($this->once())->method('getMessageParameters')->willReturn([]);

        $this->invokeMethod($handler, 'getErrorMessage', [$formError,]);

    }

    /**
     * @param string $name
     * @param EventDispatcherInterface $dispatcher
     * @param string $dataClass
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
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigInterface')->getMock();

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

    protected function invokeMethod($object, $method, array $args = [])
    {
        $reflectionMethod = new \ReflectionMethod($object, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
    }
}
