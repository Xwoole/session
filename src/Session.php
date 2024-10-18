<?php

namespace Xwoole\Session;

use OutOfBoundsException;
use Random\Randomizer;
use RuntimeException;
use Xwoole\Session\Storage\Contract as Storage;
use Xwoole\Session\Identifier\Contract as Identifier;

class Session
{
    
    private $data = [];
    private $isActive = false;
    
    public function __construct(readonly Storage $stockage, readonly Identifier $identifier)
    {
        
    }
    
    public function isClosed()
    {
        return ! $this->isActive;
    }
    
    private function generateId(): string
    {
        do
        {
            $id = bin2hex((new Randomizer)->getBytes(16));
        }
        while( $this->stockage->check($id) );
        
        return $id;
    }
    
    public function start(): void
    {
        if( $this->isActive )
        {
            throw new RuntimeException("session has been already started");
        }
        
        $this->isActive = true;
        $id = $this->identifier->get();
        
        if( empty($id) || ! $this->stockage->check($id) )
        {
            $id = $this->generateId();
            $this->identifier->set($id);
            $this->stockage->set($id, []);
            return;
        }
        
        $this->data = $this->stockage->get($id);
    }
    
    public function get(string $key)
    {
        if( ! isset($this->data[$key]) )
        {
            throw new OutOfBoundsException("invalid key '$key'");
        }
        
        return $this->data[$key];
    }
    
    public function set(string $key, string $value)
    {
        $this->data[$key] = $value;
    }
    
    public function unset(string $key)
    {
        unset($this->data[$key]);
    }
    
    public function commit()
    {
        if( $this->isActive )
        {
            $this->stockage->set($this->identifier->get(), $this->data);
        }
    }
    
    public function reset()
    {
        if( $this->isActive )
        {
            $this->data = $this->stockage->get($this->identifier->get());
        }
    }
    
    public function close()
    {
        if( $this->isActive )
        {
            $this->commit();
            $this->isActive = false;
            $this->stockage->close();
        }
    }
    
    public function regenerate()
    {
        if( $this->isActive )
        {
            $id = $this->generateId();
            $this->stockage->rename($this->identifier->get(), $id);
            $this->identifier->set($id);
        }
    }
    
    public function abort()
    {
        if( $this->isActive )
        {
            $this->isActive = false;
            $this->stockage->close();
        }
    }
    
    public function destroy()
    {
        if( $this->isActive )
        {
            $this->stockage->unset($this->identifier->get());
            $this->identifier->unset();
        }
    }
    
    public function free()
    {
        $this->data = [];
        $this->stockage->set($this->identifier->get(), []);
    }
    
}
