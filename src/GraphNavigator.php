<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;

/**
 * Handles traversal along the object graph.
 *
 * This class handles traversal along the graph, and calls different methods
 * on visitors, or custom handlers to process its nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class GraphNavigator implements GraphNavigatorInterface
{
    /**
     * @var VisitorInterface
     */
    protected $visitor;
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

    public function initialize(VisitorInterface $visitor, Context $context):void
    {
        $this->visitor = $visitor;
        $this->context = $context;

        // cache value
        $this->format = $context->getFormat();
        $this->exclusionStrategy = $context->getExclusionStrategy();
    }
}


