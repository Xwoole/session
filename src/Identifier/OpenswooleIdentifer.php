<?php

namespace Xwoole\Session\Identifier;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

readonly class OpenswooleIdentifer implements Contract
{
    
    public function __construct(
        public Request $request,
        public Response $response,
        public string $cookieName = "PHP_SESSID"
    )
    {
        
    }
    
    public function get(): string
    {
        return "";
    }
    
    public function set(string $id): void
    {
        
    }
    
    public function unset(): void
    {
        
    }
    
    
}
