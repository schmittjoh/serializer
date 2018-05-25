<?php

namespace JMS\Serializer\Exception;

class XmlErrorException extends RuntimeException
{
    private $xmlError;

    public function __construct(\LibXMLError $error)
    {
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $level = 'WARNING';
                break;

            case LIBXML_ERR_FATAL:
                $level = 'FATAL';
                break;

            case LIBXML_ERR_ERROR:
                $level = 'ERROR';
                break;

            default:
                $level = 'UNKNOWN';
        }

        parent::__construct(sprintf('[%s] %s in %s (line: %d, column: %d)', $level, $error->message, $error->file, $error->line, $error->column));

        $this->xmlError = $error;
    }

    public function getXmlError()
    {
        return $this->xmlError;
    }
}
