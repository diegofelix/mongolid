<?php

namespace Mongolid\Model\Exception;

use Exception;

class NoCollectionNameException extends Exception
{
    /**
     * Exception message.
     *
     */
    protected string $message = 'Collection name not specified into Model instance';
}
