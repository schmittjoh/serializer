<?php
/**
 * Created by PhpStorm.
 * User: Aleksander Lanes
 * Date: 23/05/2017
 * Time: 19:58
 */

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionLanguageExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getExpressions
     */
    public function testExpressionLanguageExclusionWorks($excludeIf, $expectedResult)
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');
        $metadata->excludeIf = $excludeIf;

        $context = DeserializationContext::create();

        $strat = $this->getExpressionLanguageExclusionStrategy();
        $this->assertSame($expectedResult, $strat->shouldSkipProperty($metadata, $context));
    }

    public function getExpressionLanguageExclusionStrategy()
    {
        return new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage()));
    }

    public function getExpressions()
    {
        return [
            'Test should be excluded'     => ['true', true],
            'Test should not be excluded' => ['false', false],
        ];
    }

    /**
     * @dataProvider getVariables
     */
    public function testContextAndMetadataAreAvailableAsExpressionLanguageVariables($variable)
    {
        $property_metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');
        $property_metadata->excludeIf = $variable;

        $context = DeserializationContext::create();

        $strat = $this->getExpressionLanguageExclusionStrategy();
        $this->assertSame($$variable, $strat->shouldSkipProperty($property_metadata, $context));
    }

    public function getVariables()
    {
        return [
            'Test context is available'           => ['context'],
            'Test property_metadata is available' => ['property_metadata'],
        ];
    }
}
