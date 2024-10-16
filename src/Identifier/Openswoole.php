<?php

namespace Xwoole\Session\Identifier;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OutOfBoundsException;
use RuntimeException;

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
        return $this->request->cookie[$this->cookieName] ?? "";
    }
    
    public function set(string $id): void
    {
        if( ! $this->response->isWritable() )
        {
            throw new RuntimeException("unwritable response");
        }
        
        $this->response->cookie($this->cookieName, $id, 0, "/");
    }
    
    public function unset(): void
    {
        if( ! $this->response->isWritable() )
        {
            throw new RuntimeException("unwritable response");
        }
        
        $this->response->cookie($this->cookieName, "", -1);
    }
    
}
