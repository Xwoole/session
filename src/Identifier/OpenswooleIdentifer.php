<?php

namespace Xwoole\Session\Identifier;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OutOfBoundsException;
use RuntimeException;

class OpenswooleIdentifer implements Contract
{
    private string $id;
    
    public function __construct(
        Request $request,
        readonly Response $response,
        readonly string $cookieName = "PHP_SESSID"
    )
    {
        $this->id = $request->cookie[$this->cookieName] ?? "";
    }
    
    public function get(): string
    {
        return $this->id;
    }
    
    public function set(string $id): void
    {
        if( ! $this->response->isWritable() )
        {
            throw new RuntimeException("unwritable response");
        }
        
        $this->id = $id;
        $this->response->cookie($this->cookieName, $id, 0, "/");
    }
    
    public function unset(): void
    {
        if( ! $this->response->isWritable() )
        {
            throw new RuntimeException("unwritable response");
        }
        
        $this->id = "";
        $this->response->cookie($this->cookieName, "", -1);
    }
    
    public function __toString(): string
    {
        return $this->get();
    }
    
}
