<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer;

use Symfony\Component\Yaml\Inline;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Util\Writer;

/**
 * Serialization Visitor for the YAML format.
 *
 * @see http://www.yaml.org/spec/
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class YamlSerializationVisitor extends AbstractSerializationVisitor
{
    public $writer;

    private $navigator;
    private $stack;
    private $metadataStack;
    private $currentMetadata;

    public function __construct(PropertyNamingStrategyInterface $namingStrategy, array $customHandlers)
    {
        parent::__construct($namingStrategy, $customHandlers);

        $this->writer = new Writer();
    }

    public function prepare($data)
    {
        return $data;
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->writer->reset();
        $this->stack = new \SplStack;
        $this->metadataStack = new \SplStack;
    }

    public function visitString($data, $type)
    {
        $v = Inline::dump($data);

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitArray($data, $type)
    {
        $isList = array_keys($data) === range(0, count($data) - 1);

        foreach ($data as $k => $v) {
            if (null === $v) {
                continue;
            }

            if ($isList) {
                $this->writer->writeln('-');
            } else {
                $this->writer->writeln(Inline::dump($k).':');
            }

            $this->writer->indent();

            if (null !== $v = $this->navigator->accept($v, null, $this)) {
                $this->writer
                    ->rtrim(false)
                    ->writeln(' '.$v)
                ;
            }

            $this->writer->outdent();
        }
    }

    public function visitBoolean($data, $type)
    {
        $v = $data ? 'true' : 'false';

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitDouble($data, $type)
    {
        $v = (string) $data;

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitInteger($data, $type)
    {
        $v = (string) $data;

        if ('' === $this->writer->content) {
            $this->writer->writeln($v);
        }

        return $v;
    }

    public function visitTraversable($data, $type)
    {
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$k] = $v;
        }

        return $this->visitArray($arr, $type);
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, $type)
    {
    }

    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $v = (null === $metadata->getter ? $metadata->reflection->getValue($data)
            : $data->{$metadata->getter}());

        if (null === $v) {
            return;
        }

        $name = $this->namingStrategy->translateName($metadata);

        if (!$metadata->inline) {
            $this->writer
                 ->writeln(Inline::dump($name).':')
                 ->indent();
        }

        $this->setCurrentMetadata($metadata);

        $count = $this->writer->changeCount;

        if (null !== $v = $this->navigator->accept($v, null, $this)) {
            $this->writer
                ->rtrim(false)
                ->writeln(' '.$v)
            ;
        } else if ($count === $this->writer->changeCount) {
            $this->writer->revert();
        }

        if (!$metadata->inline) {
            $this->writer->outdent();
        }
        $this->revertCurrentMetadata();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, $type)
    {
    }

    public function visitPropertyUsingCustomHandler(PropertyMetadata $metadata, $object)
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