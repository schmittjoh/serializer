<?php

namespace JMS\SerializerBundle\Metadata;

class ClassHierarchyMetadata
{
    private $classes = array();

    public function addClass(ClassMetadata $class)
    {
        $this->classes[$class->getName()] = $class;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function getLastModified()
    {
        $time = 0;

        foreach ($this->classes as $class) {
            if (false === $filename = $class->getReflection()->getFilename()) {
                continue;
            }

            if ($time < $mtime = filemtime($filename)) {
                $time = $mtime;
            }
        }

        return $time;
    }
}