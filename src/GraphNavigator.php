<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @internal
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class GraphNavigator implements GraphNavigatorInterface
{
    /**
     * @var Context
     */
    protected $context;
    /***
     * @var string
     */
    protected $format;
    /**
     * @var ExclusionStrategyInterface
     */
    protected $exclusionStrategy;

    public function initialize(Context $context): void
    {
        $this->context = $context;
        $this->format = $context->getFormat();
        $this->exclusionStrategy = $context->getExclusionStrategy();
    }
}
