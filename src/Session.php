<?php

namespace Xwoole\Session;

use ArrayAccess;
use OutOfBoundsException;
use Random\Randomizer;
use Xwoole\Session\Storage\Contract as Storage;
use Xwoole\Session\Identifier\Contract as Identifier;

class Session implements ArrayAccess
{
    
    private $dictionary = [];
    private $isActive = false;
    
    public function __construct(readonly Storage $store, readonly Identifier $id)
    {
        
    }
    
    private function generateId()
    {
        do
        {
            $this->id->set(bin2hex((new Randomizer)->getBytes(16)));
        }
        while( $this->store->check($this->id) );
    }
    
    private function save()
    {
        if( extension_loaded("igbinary") )
        {
            $data = call_user_func("igbinary_serialize", $this->dictionary);
        }
        else
        {
            $data = serialize($this->dictionary);
        }
        
        $this->store->set($this->id, $data);
    }
    
    private function load()
    {
        $data = $this->store->get($this->id);
        
        if( extension_loaded("igbinary") )
        {
            $this->dictionary = call_user_func("igbinary_unserialize", $data);
        }
        else
        {
            $this->dictionary = unserialize($data);
        }
    }
    
    private function assertClosed()
    {
        if( $this->isActive )
        {
            throw new SessionException($this, "Session has already started");
        }
    }
    
    private function assertOpened()
    {
        if( ! $this->isActive )
        {
            throw new SessionException($this, "Unavailable session");
        }
    }
    
    public function offsetExists(mixed $key): bool
    {
        $this->assertOpened();
        return array_key_exists($key, $this->dictionary);
    }
    
    public function offsetGet(mixed $key): mixed
    {
        $this->assertOpened();
        
        if( ! $this->offsetExists($key) )
        {
            throw new OutOfBoundsException("invalid key '$key'");
        }
        
        return $this->dictionary[$key];
    }
    
    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->assertOpened();
        $this->dictionary[$key] = $value;
    }
    
    public function offsetUnset(mixed $key): void
    {
        $this->assertOpened();
        unset($this->dictionary[$key]);
    }
    
    public function free()
    {
        $this->assertOpened();
        $this->dictionary = [];
        $this->save();
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    public function start(): void
    {
        $this->assertClosed();
        $this->store->open();
        $this->isActive = true;
        
        if( "" == $this->id || ! $this->store->check($this->id) )
        {
            $this->generateId();
            $this->save();
            return;
        }
        
        $this->load();
    }
    
    public function commit()
    {
        $this->assertOpened();
        $this->save();
    }
    
    public function reset()
    {
        $this->assertOpened();
        $this->load();
    }
    
    public function regenerate()
    {
        $this->assertOpened();
        $old = $this->id->get();
        $this->generateId();
        $this->store->rename($old, $this->id);
    }
    
    public function abort()
    {
        $this->assertOpened();
        $this->store->close();
        $this->isActive = false;
    }
    
    public function close()
    {
        $this->assertOpened();
        $this->save();
        $this->store->close();
        $this->isActive = false;
    }
    
    public function destroy()
    {
        $this->assertOpened();
        $this->store->unset($this->id);
        $this->id->unset();
        $this->dictionary = [];
        $this->store->close();
        $this->isActive = false;
    }
    
    public function __destruct()
    {
        if( $this->isActive )
        {
            $this->close();
        }
    }
    
}
