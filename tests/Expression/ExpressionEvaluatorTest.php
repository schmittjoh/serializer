<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Expression;

use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Expression\ExpressionEvaluator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionEvaluatorTest extends TestCase
{
    /**
     * @var ExpressionEvaluator
     */
    private $evaluator;

    public function setUp()
    {
        $this->evaluator = new ExpressionEvaluator(new ExpressionLanguage());
    }

    public function testEvaluate()
    {
        self::assertSame(3, $this->evaluator->evaluate('a + b', ['a' => 1, 'b' => 2]));
    }

    public function testParse()
    {
        $parsed = $this->evaluator->parse('a + b', ['a', 'b' ]);
        self::assertInstanceOf(Expression::class, $parsed);

        $evaluated = $this->evaluator->evaluateParsed($parsed, ['a' => 1, 'b' => 2]);
        self::assertSame(3, $evaluated);
    }

    public function testParseWitPeSetVars()
    {
        $this->evaluator->setContextVariable('a', 1);
        $parsed = $this->evaluator->parse('a + b', ['b']);

        $evaluated = $this->evaluator->evaluateParsed($parsed, ['b' => 2]);
        self::assertSame(3, $evaluated);
    }

    public function testParseWitPeSetVarsOverride()
    {
        $this->evaluator->setContextVariable('a', 1);
        $parsed = $this->evaluator->parse('a + b', ['b']);

        $evaluated = $this->evaluator->evaluateParsed($parsed, ['b' => 2, 'a' => 4]);
        self::assertSame(6, $evaluated);
    }

    public function testCanBeSerialized()
    {
        $this->evaluator->setContextVariable('a', 1);
        $parsed = $this->evaluator->parse('a + b', ['b']);

        $serializedExpression = unserialize(serialize($parsed));

        $evaluated = $this->evaluator->evaluateParsed($serializedExpression, ['b' => 2]);
        self::assertSame(3, $evaluated);
    }
}
