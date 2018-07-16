<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class FormErrorHandlerTest extends TestCase
{
    /**
     * @var FormErrorHandler
     */
    protected $handler;

    /**
     * @var JsonSerializationVisitor
     */
    protected $visitor;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    public function setUp()
    {
        $this->handler = new FormErrorHandler(new Translator('en'));
        $navigator = $this->getMockBuilder(GraphNavigatorInterface::class)->getMock();
        $context = SerializationContext::create();
        $this->visitor = (new JsonSerializationVisitorFactory())->getVisitor($navigator, $context);
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
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, []));

        self::assertSame('{}', $json);
    }

    public function testErrorHandlerWithoutTranslator()
    {
        $this->handler = new FormErrorHandler();
        $form = $this->createForm();
        $form->addError(new FormError('error!'));
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, []));

        self::assertSame(json_encode([
            'errors' => ['error!'],
        ]), $json);
    }

    public function testSerializeHasFormError()
    {
        $form = $this->createForm();
        $form->addError(new FormError('error!'));
        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, []));

        self::assertSame(json_encode([
            'errors' => ['error!'],
        ]), $json);
    }

    public function testSerializeFormWithData()
    {
        $formFactoryBuilder = Forms::createFormFactoryBuilder();
        $formFactoryBuilder->addExtension(new ValidatorExtension(Validation::createValidator()));

        $formFactory = $formFactoryBuilder->getFormFactory();
        $builer = $formFactory->createNamedBuilder('foo', FormType::class);

        $builer->add('url', TextType::class);
        $builer->add('txt', TextType::class, [
            'constraints' => [
                new Length(['min' => 10]),
            ],
        ]);

        $form = $builer->getForm();

        $form->submit([
            'url' => 'hi',
            'txt' => 'hello',
        ]);

        $data = json_encode($this->handler->serializeFormToJson($this->visitor, $form, []));
        self::assertSame('{"children":{"url":{},"txt":{"errors":["This value is too short. It should have 10 characters or more."]}}}', $data);
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

        $json = json_encode($this->handler->serializeFormToJson($this->visitor, $form, []));

        self::assertSame(json_encode([
            'errors' => ['error!'],
            'children' => [
                'child' => new \stdClass(),
                'date' => ['errors' => ['child-error']],
            ],
        ]), $json);
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

        $this->invokeMethod($handler, 'getErrorMessage', [$formError]);
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

        $this->invokeMethod($handler, 'getErrorMessage', [$formError]);
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

        $this->invokeMethod($handler, 'getErrorMessage', [$formError]);
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

        $this->invokeMethod($handler, 'getErrorMessage', [$formError]);
    }

    /**
     * @param string $name
     * @param EventDispatcherInterface $dispatcher
     * @param string $dataClass
     *
     * @return FormBuilder
     */
    protected function getBuilder($name = 'name', ?EventDispatcherInterface $dispatcher = null, $dataClass = null)
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
