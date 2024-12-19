<?php

namespace Xwoole\Session;

use ArrayAccess;
use RuntimeException;
use OutOfBoundsException;
use Random\Randomizer;
use Xwoole\Session\Storage\Contract as Storage;
use Xwoole\Session\Identifier\Contract as Identifier;
use Xwoole\Session\Serializer\Contract as Serializer;
use Xwoole\Session\Serializer\IGBinarySerializer;
use Xwoole\Session\Serializer\NativeSerializer;

class Session implements ArrayAccess
{
    
    private $dictionary = [];
    private $isActive = false;
    readonly Serializer $dumper;
    
    public function __construct(
        readonly Storage $store,
        readonly Identifier $id,
        ?Serializer $dumper = null
    )
    {
        if( null === $dumper )
        {
            try
            {
                $dumper = new IGBinarySerializer();
            }
            catch( RuntimeException )
            {
                $dumper = new NativeSerializer();
            }
        }
        
        $this->dumper = $dumper;
    }
    
    private function generateId()
    {
        do
        {
            $id = bin2hex((new Randomizer)->getBytes(16));
        }
        while( $this->store->check($id) );
        
        $this->id->set($id);
    }
    
    private function save()
    {
        $this->store->set($this->id, $this->dumper->serialize($this->dictionary));
    }
    
    private function load()
    {
        $this->dictionary = $this->dumper->unserialize($this->store->get($this->id));
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
