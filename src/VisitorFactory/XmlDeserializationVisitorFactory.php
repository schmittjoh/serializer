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

namespace JMS\Serializer\VisitorFactory;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\DeserializationVisitorInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\XmlDeserializationVisitor;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class XmlDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    private $disableExternalEntities = true;
    private $doctypeWhitelist = array();

    public function getVisitor(GraphNavigatorInterface $navigator, DeserializationContext $context): DeserializationVisitorInterface
    {
        return new XmlDeserializationVisitor($navigator, $context, $this->disableExternalEntities, $this->doctypeWhitelist);
    }

    public function enableExternalEntities(bool $enable = true): self
    {
        $this->disableExternalEntities = !$enable;
        return $this;
    }

    /**
     * @param array|string[] $doctypeWhitelist
     */
    public function setDoctypeWhitelist(array $doctypeWhitelist): self
    {
        $this->doctypeWhitelist = $doctypeWhitelist;
        return $this;
    }
}

