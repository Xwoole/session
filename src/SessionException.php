<?php

namespace Xwoole\Session;

use RuntimeException;
use Throwable;

class SessionException extends RuntimeException
{
    
    public function __construct(
        readonly Session $session,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        
    }
    
}
