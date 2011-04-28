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

namespace JMS\SerializerBundle\Serializer\Exclusion;

use JMS\SerializerBundle\Annotation\Until;
use JMS\SerializerBundle\Annotation\Since;
use Doctrine\Common\Annotations\AnnotationReader;

class VersionExclusionStrategy
{
    private $reader;
    private $version;

    public function __construct(AnnotationReader $reader, $version)
    {
        $this->reader = $reader;
        $this->version = $version;
    }

    public function shouldSkipProperty(\ReflectionProperty $property)
    {
        $annotations = $this->reader->getPropertyAnnotations($property);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Since && version_compare($this->version, $annotation->getVersion(), '<')) {
                return true;
            } else if ($annotation instanceof Until && version_compare($this->version, $annotation->getVersion(), '>')) {
                return true;
            }
        }

        return false;
    }
}