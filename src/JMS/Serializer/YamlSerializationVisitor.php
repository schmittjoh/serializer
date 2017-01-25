<?php

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

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use Symfony\Component\Yaml\Inline;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Util\Writer;

/**
 * Serialization Visitor for the YAML format.
 *
 * @see http://www.yaml.org/spec/
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class YamlSerializationVisitor extends AbstractVisitor
{
    public $writer;

    private $navigator;
    private $stack;
    private $metadataStack;
    private $currentMetadata;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy, AccessorStrategyInterface $accessorStrategy = null)
    {
        parent::__construct($namingStrategy, $accessorStrategy);

        $this->writer = new Writer();
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->writer->reset();
        $this->stack = new \SplStack;
        $this->metadataStack = new \SplStack;
    }

    public function visitNull($data, array $type, Context $context)
    {
        if ('' === $this->writer->content) {
            $this->writer->writeln('null');
        }

        return 'null';
    }

    public function visitString($data, array $type, Context $context)
    {
        $v = Inline::dump($data);

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    /**
     * @param array $data
     * @param array $type
     */
    public function visitArray($data, array $type, Context $context)
    {
        $count = $this->writer->changeCount;
        $isList = (isset($type['params'][0]) && ! isset($type['params'][1]))
            || array_keys($data) === range(0, count($data) - 1);

        foreach ($data as $k => $v) {
            if (null === $v && $context->shouldSerializeNull() !== true) {
                continue;
            }

            if ($isList) {
                $this->writer->writeln('-');
            } else {
                $this->writer->writeln(Inline::dump($k).':');
            }

            $this->writer->indent();

            if (null !== $v = $this->navigator->accept($v, $this->getElementType($type), $context)) {
                $this->writer
                    ->rtrim(false)
                    ->writeln(' '.$v)
                ;
            }

            $this->writer->outdent();
        }

        if ($count === $this->writer->changeCount && isset($type['params'][1])) {
            $this->writer
                ->rtrim(false)
                ->writeln(' {}')
            ;
        } elseif (empty($data)) {
            $this->writer
                ->rtrim(false)
                ->writeln(' []')
            ;
        }
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $v = $data ? 'true' : 'false';

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $v = (string) $data;

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $v = (string) $data;

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        $v = $this->accessor->getValue($data, $metadata);

        if (null === $v && $context->shouldSerializeNull() !== true) {
            return;
        }

        $name = $this->namingStrategy->translateName($metadata);

        if ( ! $metadata->inline) {
            $this->writer
                 ->writeln(Inline::dump($name).':')
                 ->indent();
        }

        $this->setCurrentMetadata($metadata);

        $count = $this->writer->changeCount;

        if (null !== $v = $this->navigator->accept($v, $metadata->type, $context)) {
            $this->writer
                ->rtrim(false)
                ->writeln(' '.$v)
            ;
        } elseif ($count === $this->writer->changeCount && ! $metadata->inline) {
            $this->writer->revert();
        }

        if ( ! $metadata->inline) {
            $this->writer->outdent();
        }
        $this->revertCurrentMetadata();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
    }

    public function setCurrentMetadata(PropertyMetadata $metadata)
    {
        $this->metadataStack->push($this->currentMetadata);
        $this->currentMetadata = $metadata;
    }

    public function revertCurrentMetadata()
    {
        return $this->currentMetadata = $this->metadataStack->pop();
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function getResult()
    {
        return $this->writer->getContent();
    }
}
