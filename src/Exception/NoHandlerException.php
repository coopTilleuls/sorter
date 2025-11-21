<?php

namespace Sorter\Exception;

final class NoHandlerException extends \RuntimeException implements SorterException
{
    public function __construct(string $requestType)
    {
        parent::__construct('No handler found for request type: ' . $requestType);
    }
}
