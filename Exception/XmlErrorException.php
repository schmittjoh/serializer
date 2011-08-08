<?php

namespace JMS\SerializerBundle\Exception;

class XmlErrorException extends RuntimeException
{
    private $xmlError;

    public function __construct(\LibXMLError $error)
    {
        parent::__construct(sprintf('%d: Could not parse XML: %s in %s (line: %d, column: %d)', $error->level, $error->message, $error->file, $error->line, $error->column));

        $this->xmlError = $error;
    }

    public function getXmlError()
    {
        return $this->xmlError;
    }
}