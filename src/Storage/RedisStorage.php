<?php

namespace Xwoole\Session\Storage;

use OutOfBoundsException;
use Redis;
use RuntimeException;

class RedisStorage implements Contract
{
    
    public function __construct(
        readonly Redis $dbc,
        $host,
        $port = 6379,
        $timeout = 0.0,
        $reserved = null,
        $retryInterval = 0,
        $readTimeout = 0.0
    )
    {
        $dbc->connect($host, $port, $timeout, $reserved, $retryInterval, $readTimeout);
    }
    
    public function check(string $id): bool
    {
        return $this->dbc->exists($id);
    }
    
    public function get(string $id): string
    {
        if( ! $this->dbc->exists($id) )
        {
            throw new OutOfBoundsException;
        }
        
        return $this->dbc->get($id);
    }
    
    public function set(string $id, string $data): void
    {
        $this->dbc->set($id, $data);
    }
    
    public function rename(string $oldId, string $newId): void
    {
        $this->dbc->rename($oldId, $newId);
    }
    
    public function unset(string $id): void
    {
        $this->dbc->del($id);
    }
    
    public function close(): void
    {
        $this->dbc->close();
    }
    
}
