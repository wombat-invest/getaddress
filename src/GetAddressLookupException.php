<?php

namespace WombatInvest\GetAddress;

use Exception;

class GetAddressLookupException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
